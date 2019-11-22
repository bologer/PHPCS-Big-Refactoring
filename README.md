# PHPCS PhpStorm/Webstorm Opener

## Purpose of This Tool
Image you've been given a task to introduce PHP sniffer for some reasonably big project.

Surely you can use some automatic tools to make auto corrections on what is possible to correct (e.g. phpcbf, but it does only basic fixes).

What is left is done by hands. 

Normally, developer goes as follows: 
- run phpcs, check errors
- copy file path, find in IDE
- fix error 

I was asking myself, how annoying it is to do it on repeat. Why can't I just click on path produced by phpcs and open it in IDE? 

So it is possible and this repo is to solve it. 

## Installation 


Clone repo: 


```
mkdir /tmp/phpcs
git clone https://github.com/bologer/PHPCS-Big-Refactoring.git /tmp/phpcs
```

Generate launcher: 

- Open PhpStorm or Webstorm
- Click "Tools" -> "Generate Command-Line Launcher..."


Run phpcs and save output into some file: 

```
./vendor/bin/phpcs -p --colors --encoding=utf-8 --extensions=php your_folder >> /tmp/phpcs/phpcs-output.txt
```


Run parser: 

```
php parser.php parse /tmp/phpcs/phpcs-output.txt
```


Open PhpStorm/Webstorm and run: 

```
php parser.php next
```

It should open file for correction. Run same command to continue.

## How It Works? 
It is up to you to configure phpcs and run linter on your code. 

This library only helps you to work with errors a bit smoother then normal.

Example phpcs output: 

```
FILE: /some/file/path/to/PhpFile.php
-------------------------------------------------------------------------------------
FOUND 3 ERRORS AFFECTING 3 LINES
-------------------------------------------------------------------------------------
 170 | ERROR | Line exceeds maximum limit of 120 characters; contains 132 characters
 189 | ERROR | Line exceeds maximum limit of 120 characters; contains 135 characters
 208 | ERROR | Line exceeds maximum limit of 120 characters; contains 150 characters
-------------------------------------------------------------------------------------


FILE: /some/other/file/path/to/PhpFile.php
-------------------------------------------------------------------------------------
FOUND 1 ERROR AFFECTING 1 LINE
-------------------------------------------------------------------------------------
 104 | ERROR | Line exceeds maximum limit of 120 characters; contains 128 characters
-------------------------------------------------------------------------------------
```

You can put it into some file (e.g. `phpcsoutput.txt`) and then run the following command to parse it: 

```
php parser.php parse /path/to/phpcsoutput.txt 
```

`parser.php` processing this output and saves JSON list of file paths in `parsed-output.json`.



 

Then you can open PhpStorm or Webstorm and run the following command: 

```
php parser.php next
```

This command grabs JSON from `parsed-output.json`, converts to array, grabs last item, runs launcher with file path 
and array_pop last item, assuming that developer would fix file before moving to the next one.

Run this command until all of the files fixed


## Commands 


Parse phpcs output: 

```
php parser.php parse /file/to/phpcsoutput.txt
``` 

Move through parsed list: 

```
php parser.php next
```