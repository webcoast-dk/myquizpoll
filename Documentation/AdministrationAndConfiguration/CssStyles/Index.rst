

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


CSS Styles
^^^^^^^^^^

As usual, the default CSS styles may be copied to a .css file. In such
a case, the location of the file should be specified by TS setup:

plugin.tx\_myquizpoll\_pi1.\_CSS\_DEFAULT\_STYLE
>page.includeCSS.file1 = fileadmin/styles/quiz\_extensions.css

You can find the default CSS styles in the following file:
“pi1/static/setup.txt”. You will find a “dummy.css” file with all used
selectors in that directory too! Otherwise you can override the styles
like this:

plugin.tx\_myquizpoll\_pi1.\_CSS\_DEFAULT\_STYLE
(.tx\_myquizpoll\_pi1-title { font-size: 12pt; font-weight: bold;
color: #000; })

