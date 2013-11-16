### A PHP parser for Concord

A standalone Concord publisher with a PHP parser to render OPML files. Use as a lightweight blogging system on your own web server.

### What it does

+ One outline = one post
+ Drops OPML files on the server, one file per post
+ OPML files are named as `concord-posttitle.opml`
+ Builds a page of posts from multiple OPML files
+ Most recent posts are displayed first
+ Two sample OPML files are included
+ Retains toolbars similar to <a href="http://fargo.io">Fargo</a>'s 
+ Like <a href="http://fargo.io">Fargo</a>, store photos elsewhere and reference 
+ Uses localStorage for fast editing
+ Uses PHP DOM for robust XML parsing
+ Upgraded `concord.js` for fontawesome 4.0.1
+ Minor tweaks of `concord.js` and `concordUtils.js`

### Installation

1. Place files on a web server (either in root or a subdirectory)
2. Edit `concord-ui.php` here:
    1. Change the location of files in `<head>` references
    2. Under `var appConsts` change `domain: http://www.example.com` to your web server
     
### How to use

*Writing:*    

1. From a web browser, go to http://www.example.com/concord-ui.php (or your subdirectory)
2. Start typing
3. Name the post title, then click Save Title
4. When finished, click Save Work
5. To publish a post, click Export OPML
6. To write a new post, click Clear All Data/New

*Output:*

1. From a web browser, go to http://www.example.com/viewopml.php (or your subdirectory)

This renders and displays the OPML output, a list of nodes and attributes, and sample HTML. Basically, the full OPML structure to help you grasp DOM parsing, and is ready for your customization.

### More about Concord

This is a fork of the main <a href="https://github.com/scripting/concord">Concord</a> outliner project. <a href="http://docs.fargo.io/outlinerHowto">An outliner</a> is a text editor that organizes information in a hierarchy, allowing users to control the level of detail and to reorganize according to structure. Your notes can have full detail, yet be organized so a casual reader can get a quick overview. Outlining is a great way for teams to organize work. 
<i><a href="http://scripting.com/2013/09/16/concordOurGplOutliner">Dave Winer</a>, 9/16/13.</i>


### GPL-licensed

<a href="https://github.com/scripting/concord">Concord</a> is licensed under the GPL because outliners are an incredibly useful way to edit structured information. We want Concord to be able to fill every conceivable need for outlining technology. 

Ideas include file systems, mailboxes, chatrooms, databases, documents, presentations, product plans, code, libraries, laws, systems of laws, contracts, rules, guidelines, principles, docs, manifestos, journals, blogs, etc. 

Here's an important 11-minute <a href="http://scripting.com/2013/09/17/importantPodcastAboutConcordGpl">podcast</a> about Concord and the GPL.  


### Community

We have a <a href="https://groups.google.com/forum/?fromgroups#!forum/smallpicture-concord">Google Group mail list</a> for technical support.



