InMaFSS
=======
######Information Management for School Systems  

---

InMaFSS was created to help my school to display the contents of it's substitution plan.
One of the most important requirements was to create a lightweight Javascript script being able of flipping through the replacements instead of scrolling, which was done by the former system [*Indiss*](http://sourceforge.net/projects/indiss/).
The scrolling used quite a lot of CPU, causing the very small client PC displaying the plan to crash.

This is why InMaFSS is aimed to process most data upon the server and only give the client very little to process.

Currently I'm developing upon a new REST-API, so I recommend against using the current API as it won't be supported for long anymore. Please also  consider that the new API will use OAuth1.0a to authenticate single users in order to make it possible to get only user-related substitutions.

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