# GAIA

GeoCMS Artificial Intelligence Applications

## TOOLKIT

* GAIA is a toolkit for PHP developers

* Start a PHP local web server 

```
php gaia.php server::run

```

* Create an empty class file

```
php gaia.php code::write MyClass

```

* Launch a headless browser and take screenshot
  * parameters are stored in JSON files

```
php gaia.php chromium::web my-data/web-001.json

```

* CMS (under construction...)
  * OnePage
  * Small Business
  * Blog
  * CMS
  * Marketplace

  
## Application Streaming

* Shortest path from coder to user
* Load only what is needed
* No need to install anything

### Web Application

### JS Modules

* Dynamic loading
* Load only what is needed
  
### PHP class autoloader

* Dynamic loading
* Load only what is needed
* Class definition can be
  * in PHP files
  * in ZIP archives
  * in SQL database
  * ...



### GeoCMS 4D

* 4 dimensions: x,y,z,t

### GLB format

* 3D model format
  * JSON GLTF
* Animations
* Archive of all assets

[bottle](media/glb/bottle.glb)


### UTILS

#### github hooks

* You can add a hook to your github repository
* So that when you push to github, the hook will be called
* This can be used to update your website automatically
* https://github.com/YOUR-USER/YOUR-REPO/settings/hooks


### AFRAME

* Build a 3D scene in a web page
* https://aframe.io/docs/1.3.0/guides/building-a-basic-scene.html
