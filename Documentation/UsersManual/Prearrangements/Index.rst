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


Prearrangements
^^^^^^^^^^^^^^^

1. Install the extension using the Extension Manager.2. Create a standard-page.3. Create a pagecontent and choose the type “Insert
plugin”. Choose the plugin “My Quiz and Poll”.You can although use the pagecontent-wizard:

|img-7|

*Image 4: Using the Pagecontent-wizard*

Now you can create the questions of your quiz on this new page, but we
recommend that you first create a page of type SysFolder that will
contain your questions and answers. The title of the SysFolder will
not be used (except for ###VAR\_FOLDER\_NAME###).

|img-8|

*Image 5: Using a SysFolder*

Finally you must tell the quiz-page where to seek for questions. If
you use a SysFolder you have to select him from the page-content of
the quiz-page:

|img-9|

*Image 6: Where to seek for the questions?*
