# Joomla 3.x sample custom folder for oneContent

This repository contains the contents of `<JPATH_SITE>/media/one`. 

## Development install

Assuming you have a repository for your site, use these commands:

```
  $ cd <JPATH_SITE>
  $ git submodule add https://github.com/pdelbar/one-content-sample-custom.git media/one  
  $ git submodule init
  $ git submodule update
```

Make sure to commit the .gitmodules file. [Working with modules is explained here](https://chrisjean.com/2009/04/20/git-submodules-adding-using-removing-and-updating/:).
