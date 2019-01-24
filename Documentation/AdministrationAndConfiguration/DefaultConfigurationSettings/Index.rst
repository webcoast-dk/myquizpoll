.. include:: Images.txt

.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

.. ==================================================
.. DEFINE SOME TEXTROLES
.. --------------------------------------------------
.. role::   underline
.. role::   typoscript(code)
.. role::   ts(typoscript)
   :class:  typoscript
.. role::   php(code)


Default configuration/settings
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

In order to use the default CSS Styles and the default settings you
have to include both default configuration files. You can do this in
the Template view mode. Select there the menu item “Info/Modify”. Then
click on the button “Click here to create an extension template.” Then
click on the link “Click here to edit whole template record.” Scroll
down to the record “Include static (from extenstions):”. Select the
myquizpoll items. “default styles” and “star rating” is optional.

|img-12|

*Image 9: Include at least "default settings"*

The content of this files:- default styles contains the StyleSheets
and language files.- default settings: contains the default settings
(TypoScript).- star rating (question type) contains: JavaScript and
Style-sheets. You need it only, if you use the question-type “Star
rating”.- star rating (rating) contains: JavaScript and Styles for the
rating example with stars. Don't include both rating TypoScripts!If
you want to change the settings easily, you must include the default
settings-file anyway!

