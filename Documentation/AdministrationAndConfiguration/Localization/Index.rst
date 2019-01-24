

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


Localization
^^^^^^^^^^^^

You may adapt to your needs and languages the labels in
“pi1/locallang.xml”. Any label can be overridden by inserting the
appropriate assignment in your TS template setup:

plugin.tx\_myquizpoll\_pi1.\_LOCAL\_LANG. *languageCode.labelName* =
*overridingValue*

An example:plugin.tx\_myquizpoll\_pi1.\_LOCAL\_LANG.en.no\_name =
Insert your name here

You can find the name of the label you want to modify (or translate)
by inspecting the extension file “pi1/locallang.xml”.

If you want to change the labels of the backend-fields, you need the
name of the database-field. Then you can change it via “Page TSConfig”
of the folder with the questions. Example for the points-field:

TypoScript: TCEFORM.tx\_myquizpoll\_question.points.label = Euro-
Betrag

