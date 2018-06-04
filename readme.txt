NOTE: This was written in 2011 as incentive to learn php, and hosted at sourceforge. Still running on sites around the world.


author:
----------------------------------------------------------------------------------------------------------
Yuval Leshem
yuval.leshem@gmail.com

about:
----------------------------------------------------------------------------------------------------------
This is a very simple gallery API for management and viewing.
requires php and mysql, which there are loads of free web hosts to.
example management and viewing clients are included.

installation:
----------------------------------------------------------------------------------------------------------
1. Unzip the gallery.zip files to you web host directory. If you want to use the sample client,
   Unzip client.zip as well.
2. Create a database. Write down database host, database name, username and password
3. Browse to [DIR]/install.php . Fill in the parameters, and you're ok.

example mamagement client:
----------------------------------------------------------------------------------------------------------
I wrote a very simple HTML management portal, so you can start off from there.
Launch point is /index.php.
Read-only (viewer) simple sample client is /viewer.html, so you can easily learn the viewing api.

version 2:
----------------------------------------------------------------------------------------------------------
1. All cms calls are via api.php
2. Better client. jquery use. flickr inspired ui.
3. News items (Name and description only).


2.0.1:
1. Back to regular buttons (Due to lack of IE9 CSS support)
2. Click and edit - On click, focus on edited item
3. Other fixes

2.0.2:
1. nl2br_js function added for line break support
2. some sql_safe was missing from api
3. delete install.php after installation (security issue)

2.0.3:
1. Ability to upload attachments to news items!


Please feel free to mail with questions.

Yuval

