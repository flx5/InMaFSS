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

Installation of OAuth
------------
- First we have to install some requirements. This can be done on Ubuntu with a command like this:

`apt-get install libpcre3-dev`

- Then we can install the oauth extension itself:

`pecl install oauth`

On Windows you can obtain a copy of a working DLL from [here](http://pecl.php.net/package/oauth).


Now on LINUX AND WINDOWS check your php.ini file.

Search for a line containing `extension=oauth`.
If it starts with a `;` remove that to uncomment the line.
If you can't find the line add it in the section where all the other extensions are placed.

Libraries
---------

+ [TinyMCE](http://www.tinymce.com) (4.0.16)
+ [Tigra Calendar](http://www.softcomplex.com/products/tigra_calendar/) (v5.2)      
+ [oauth-php](http://code.google.com/p/oauth-php/) (175)