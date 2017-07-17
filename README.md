moddir_autoindex
================
This modul is a replacement for the Apache-Server-Module mod_autoindex.
Implementation is just as cgi-script without any defined server-extension,
but requires "mod_dir" which usually is enabled anyway.
"mod_autoindex" can be deactivated but is not triggered anymore with this script.

1) Usage and Implementation
2) Deactivating "mod_autoindex"
3) Configuration

1) Usage and Implementation
---------------------------
This script is called by directive DirectoryIndex.
This whole folder should be placed in a folder for cgi-usage inside the web-directory.
In a xampp-installation this could be this path:
	C:\xampp\htdocs\cgi-bin
So if you never have a folder "cgi-bin" in your web-directory (htdocs), create it and
place this project's mai-folder inside.

Now open the apache-configuration file which in linux could be here:
	/etc/apache2/conf/httpd.conf
in xampp it could be here:
	C:\xampp\apache\conf\httpd.conf
	
The only adjustment in this file is the line with the directive "DirectoryIndex".
Below are several examples and for readability linebreaks can be included in this line.
To include linebreaks just add a backslash "\" at the position where you want to break the line and
break everything after this backslash in the new line, you can see it in the first example:

<IfModule dir_module>
    DirectoryIndex index.php index.pl index.cgi index.asp index.shtml index.html index.htm \
                   default.php default.pl default.cgi default.asp default.shtml default.html default.htm \
                   home.php home.pl home.cgi home.asp home.shtml home.html home.htm \
				   /cgi-bin/moddir_autoindex/php/index.cgi
</IfModule>

Surly it's possible to shorten the list like this:

<IfModule dir_module>
    DirectoryIndex index.php /cgi-bin/moddir_autoindex/php/index.cgi
</IfModule>

In this example the cgi-file is extecuted if no file index.php is found,
for any other files is not searched by the server.
Just include there all the files that shall or could serve as index,
a compromise might be sensful, but even the following example should be adjusted to the needs:

<IfModule dir_module>
    DirectoryIndex index.php index.pl index.cgi index.asp index.shtml index.html index.htm /cgi-bin/moddir_autoindex/php/index.cgi
</IfModule>

Ao you might wish to remove index.asp or add index.aspx.

Configuration is described in another file.

2) Deactivating "mod_autoindex"
-------------------------------

3) Configuration
----------------

