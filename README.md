# Start service

Ensure your docker works from that user, e.g. `docker pull openalpr/openalpr`

	go run sg-anpr.go

# Test service

	wget http://s.natalian.org/2016-10-02/SKC6322K.jpg
	curl -F "file=@SKC6322K.jpg" http://localhost:7077/upload

* [Test plate](http://s.natalian.org/2016-10-02/SKC6322K.jpg)
* [Video explaining how it works](https://www.youtube.com/watch?v=fpj6vptUbCA)
* [Video showing how it was built](https://www.youtube.com/watch?v=pKD4cgRayJk)
* [More training images needed](https://groups.google.com/forum/#!msg/openalpr/oWU2CvTR7yU/TEsz9LUgBQAJ)
* [Wikipedia article on Singapore license plates](https://en.wikipedia.org/wiki/Vehicle_registration_plates_of_Singapore)

# Caddy configuration for demo

	anpr.dabase.com {
		proxy / localhost:7077
		log stdout
		errors stdout
	}

# TODO

Speed it up by going direct to running bindings
https://github.com/openalpr/openalpr/tree/master/src/bindings/go

Have a way to give feedback if it was correct or not.

Do Malaysian version! <https://www.youtube.com/watch?v=CWoGfMMpRFM> Idea being
we can crowd source Malaysian plates!  <http://paultan.org/2016/09/28/554748/>
