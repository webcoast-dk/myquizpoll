

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


Dynamic Template Scopes
^^^^^^^^^^^^^^^^^^^^^^^

If you use the TSconfig-variable "showAnalysis” you will probably need
to add or modify some templates. You can set with this variable which
template should be shown at the end of the quiz. It depends on the
reached percentage value which template will be shown. The TS-variable
"showEvaluation” depends on reached scores.

Example 1: if you set

"showAnalysis = 0,33.34,66.67,100"

then the quiz taker will see one of the templates TEMPLATE\_QUIZ\_ANALYSIS\_0,
TEMPLATE\_QUIZ\_ANALYSIS\_33.34,TEMPLATE\_QUIZ\_ANALYSIS\_66.67 or
TEMPLATE\_QUIZ\_ANALYSIS\_100. You must define this templates in the
template file! Example: the quiz taker gets 60% of all scores then he
will see the content of the template
“TEMPLATE\_QUIZ\_ANALYSIS\_66.67”. For more informations read the text
in the example template file too
(examples/template\_analysis.html).

Example 2: if you set

"showAnalysis = 25:1,50:2,75:3,100:4"

then the quiz taker will see one of the pages
with the UID 1,2,3 or 4. E.g. 51-75%: redirect to page with the UID 3.
In this case you don´t need this special templates.

Example 3: if you set

"showAnalysis = 99:0,100:4"

then there will be no redirect if not
all questions were answered correctly!

Example 4: if you set

"showEvaluation = 25:1,50:2,75:3,100:4"

then the quiz taker will see one of the tt\_content elements with the UID 1,2,3 or 4. E.g. 51-75
scores: show the tt\_content element with the UID 3. In this case you
don´t need this special templates.

