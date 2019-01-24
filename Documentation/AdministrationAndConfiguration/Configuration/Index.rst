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


Configuration
^^^^^^^^^^^^^

You can configure the plugin via Flexforms like shown in chapter
“Prearrangements”.But note: you can not configure everything via
flexforms! TypoScript offers you more possibilities.Note 2: the
flexform-values will override the TypoScript-values.

If you configure the plugin with TypoScript I recommend you to use the
“TypoScript Object Browser” in the Template view mode. You will see
there all variables:

|img-13|

*Image 10: Configure your quiz or poll here*

See chapter “TypoScript Reference” for more information about this variables! Click on a row, if you want to change a value:

|img-14|

*Image 11: How you can change a value...*

If you want to show the highscore-list or poll-result on a second
page, you can do this by using the CMD property. If you set the value
to “score” there will be shown only the highscore-list or if you set
the value to “list” there will be shown only the poll-result on that
second page. You can do this if you want that the highscore-list is
visible/clickable in your menu. “archive” shows you a list of old
polls in the basic poll mode.

