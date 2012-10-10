
# Sledgehammer Graphics module

## Features

* Compose images via layers.
* Use CSS notation for colors "white", "#ff8800" or "rgba(255,130, 0, 0.5)"
* Use CSS notation for text "bold 18px Arial, sans-serif"
* Autodetect filetype and support for bmp.

## Datastructure

Modelled after Adobe Photoshop's layers & folders.

* Composition
    * TextGraphics
	* Composition
      * Canvas
      * Image
    * Image

## Creating an image object

## Example usage

```php
     $image = new Image('/path/to/my-image.jpg');
     $resized = $image->resized(120, 100);
     $resized->saveTo('/path/to/my-image-as-120x100.png');
```

