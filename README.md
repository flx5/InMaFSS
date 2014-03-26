InMaFSS
=======
######Information Management for School Systems  

---

InMaFSS was created to help my school to display the contents of it's substitution plan.
One of the most important requirements was to create a lightweight Javascript script being able of flipping through the replacements instead of scrolling, which was done by the former system [*Indiss*](http://sourceforge.net/projects/indiss/).
The scrolling used quite a lot of CPU, causing the very small client PC displaying the plan to crash.

This is why InMaFSS is aimed to process most data upon the server and only give the client very little to process.

This release should run stable, but there are lots of things to be improved. 
The features capable of being improved are:

+ OAuth User Panel (If the user hasn't authenticated any consumer, the page looks quite empty :) )
+ Improve the API and add an endpoint to get some information about the user (like username, class, usertype, etc.)
+ Design?

Licencing
---------

The licence included in this project only affects the files that are part of this project.
The libraries still underlie their own licences. 

Please note that a missing licence header in a file that is part of InMaFSS doesn't mean that it is not included in the licence. It only means that I've forgot to add the header to the file.

Requirements
------------
+ >= PHP 5.2.3
+ mysql/mysqli

Libraries
---------

+ [TinyMCE](http://www.tinymce.com) (4.0.16)
+ [Tigra Calendar](http://www.softcomplex.com/products/tigra_calendar/) (v5.2)      
+ [ip_in_range](http://www.pgregg.com/projects/php/ip_in_range/) (v1.2)
+ [OAuth2 Server](https://github.com/bshaffer/oauth2-server-php) (v0.9) (Using an old version to ensure compability with pre PHP 5.3.9
+ [ics-parser] (http://code.google.com/p/ics-parser/)
