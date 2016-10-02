package main

import (
	"bytes"
	"html/template"
	"io"
	"log"
	"net/http"
	"os"
	"os/exec"
	"path"
	"strconv"
	"time"

	"github.com/otiai10/marmoset"
)

func uploadHandler(w http.ResponseWriter, r *http.Request) {

	render := marmoset.Render(w, true)

	file, _, err := r.FormFile("file")
	if err != nil {
		render.JSON(http.StatusBadRequest, err)
		return
	}
	defer file.Close()

	buff := make([]byte, 512) // why 512 bytes ? see http://golang.org/pkg/net/http/#DetectContentType
	_, err = file.Read(buff)
	if err != nil {
		render.JSON(http.StatusBadRequest, err)
		return
	}

	filetype := http.DetectContentType(buff)
	if filetype != "image/jpeg" {
		render.JSON(http.StatusBadRequest, map[string]string{"error": filetype + " detected, not a image/jpeg"})
		return
	} else {
		// reset for io.Copy
		_, err = file.Seek(0, 0)
		if err != nil {
			render.JSON(http.StatusInternalServerError, err)
			return
		}

	}

	tmpfile, err := os.Create("tmp/" + strconv.FormatInt(time.Now().UTC().Unix(), 10) + ".jpg")
	if err != nil {
		render.JSON(http.StatusInternalServerError, err)
		return
	}

	if _, err := io.Copy(tmpfile, file); err != nil {
		render.JSON(http.StatusInternalServerError, err)
		return
	}
	tmpfile.Close()

	pwd, _ := os.Getwd()
	log.Println("docker", "run", "--rm", "-v", pwd+"/tmp:/data:ro", "openalpr/openalpr", "-j", "-c", "sg", path.Base(tmpfile.Name()))
	cmd := exec.Command("docker", "run", "--rm", "-v", pwd+"/tmp:/data:ro", "openalpr/openalpr", "-j", "-c", "sg", path.Base(tmpfile.Name()))
	var stdout bytes.Buffer
	var stderr bytes.Buffer
	cmd.Stdout = &stdout
	cmd.Stderr = &stderr
	err = cmd.Run()
	if err != nil {
		http.Error(w, err.Error()+"\n"+stderr.String(), http.StatusInternalServerError)
		return
	}

	w.WriteHeader(http.StatusOK)
	w.Header().Set("Content-Type", "application/json")
	w.Write(stdout.Bytes())
}

func index(w http.ResponseWriter, r *http.Request) {
	templates := template.Must(template.New("main").ParseGlob("*.html"))
	err := templates.ExecuteTemplate(w, "index.html", nil)
	if err != nil {
		log.Panic(err)
	}
	log.Printf("%s %s %s %s\n", r.RemoteAddr, r.Method, r.URL, r.UserAgent())
}

func main() {
	http.HandleFunc("/", index)
	http.HandleFunc("/recognise", uploadHandler)
	http.ListenAndServe(":7077", nil)
}
