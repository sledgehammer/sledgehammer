
# Sledgehammer MVC

MVC aka Model View Controller


## Model

These are the classes you'll write yourself.
If your looking for database persistance for your models check out the Sledgehammer ORM module.


## View

De "View" wordt d.m.v. Views opgebouwd.
Deze view objects zijn te vergelijken met een ASP.NET UserControl

Een View heeft een "render" functie. Deze stuurt output direct naar de browser (echo) en heeft geen returnwaarde.
En View class *mag* een getHeaders() functie bevatten, deze returnt informatie die voor het component verstuurd dient te worden.
Denk aan "HTTP headers", stylesheets en andere elementen voor in de <head>


## VirtualFolders (Controller)

In tegenstelling tot andere MVC frameworks, heeft Sledgehammer MVC geen direct routing mechanisme.

De controllers die gebruikt worden, wordt bepaald door de url.
Als er meerdere mappen in een url zitten worden er meerdere VirtualFolders aangeroepen.

De url "/blog/author.html" gaat bijvoorbeeld door 2 VirtualFolders.

Alles gaat naar het MyWebsite class (extends Website),
deze zal dan een "MyBlogFolder" class (extends VirtualFolder) aanroepen.
En deze zal dan de Content/Component voor de author.html genereren.

Het website object breidt dit component vervolgens uit met een layout en menu.



TODO:
*_folder()
en *() uitleggen.

De url "/" gaat naar het Website object en wordt als "index.html" behandeld en zoekt naar een index() functie.

Functies in een VirtualFolder kunnen

### Component->getHeaders()

De getHeaders() functie geeft een array die de volgende keys kan bevatten.
'http'  Dit is een array die met de header() verstuurd zullen worden.
'meta'  Dit is een array die als <meta> tag(s) in de <head> wordt toegevoegd
'css'   Dit is een array met urls die als als <link type="text/css"> in de <head> wordt toegevoegd
'link'  Dit is een array die als <link> tag(s) in de <head> wordt toegevoegd
'title' Dit is de <title> die in de <head> wordt gezet.

### Rollen

#### Website
Het volledig afhandelen van request.

Versturen naar browser
Opslaan op schijf

#### HtmlDocument
De waardes van getHeaders() verwerken in de doctype template.

## Installation

Place the mvc folder in the same folder as Sledgehammer's core folder.

To generate a scaffolding for an MVC project, run
```
php sledgehammer/utils/empty_project.php
```

## Twitter Bootstrap

Contrains all the css & javascript from: http://twitter.github.com/bootstrap/ and adds Sledgehammer\View classes.

```
$pagination = new Pagination(5, 1);
```

Becomes:

```
<div class="pagination">
	<ul>
		<li class="disabled"><a href="#">«</a></li>
		<li class="active"><a href="?page=1">1</a></li>
		<li><a href="?page=2">2</a></li>
		<li><a href="?page=3">3</a></li>
		<li><a href="?page=4">4</a></li>
		<li><a href="?page=5">»</a></li>
	</ul>
<div>
```