

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


Your own variables
^^^^^^^^^^^^^^^^^^

Youcan declare own variables in TypoScript for your own template. This
variables are here valid: page, questions, answers and the
highscore/poll result list. This is important for even/odd
interpretations. You can use “plugin.tx\_myquizpoll\_pi1.myVars.page.”
in TEMPLATE\_QUESTION\_PAGE and TEMPLATE\_RESULT\_PAGE,
“plugin.tx\_myquizpoll\_pi1.myVars.questions.” for questions,
“plugin.tx\_myquizpoll\_pi1.myVars.answers.” for answers and
“plugin.tx\_myquizpoll\_pi1.myVars.list.” for the highscore and the
poll result list.

This variables can be used in your template at this way:
###MY\_YOURVARIABLE###. Replace YOURVARIABLE with the name of your
variable. Recommendation: take a look at the
“template\_analysis.html”.

*:underline:`Note`* : MY\_SELECT (select-box), MY\_OPTION (select-
option), MY\_INPUT\_TEXT (text-answer), MY\_INPUT\_AREA (text-area),
MY\_INPUT\_RADIO (radio-button), MY\_INPUT\_CHECKBOX (checkbox),
MY\_INPUT\_WRAP (wrap the text of input-fields), MY\_INPUT\_ID and
MY\_INPUT\_LABEL are reserved for questions and/or answers and can be
set via TypoScript.Radio-buttons and check-boxes have no class-
attribute. This is the only way to give them a class-attribute!Yes-no-
boxes are automatically wrapped by the the classes
tx\_myquizpoll\_pi1-yesno, -yes and -no.

Example 1:

TypoScript: myVars.questions.eo\_align =
tx\_myquizpoll\_pi1-right,tx\_myquizpoll\_pi1-left

HTML-Template, area ###TEMPLATE\_QUESTION###: <div
class="###MY\_EO\_ALIGN###">

This means: ###MY\_EO\_ALIGN### will be replaced by
“tx\_myquizpoll\_pi1-right” for odd questions (question 1,3,5,...) and
“tx\_myquizpoll\_pi1-left” for even questions (question 2,4,6,...).

Example 2:

TypoScript: myVars.answers.align = left,center,right,center,left

HTML-Template, area ###TEMPLATE\_QRESULT###: <p
align="###MY\_ALIGN###">

This means: ###MY\_ALIGN### will be replaced by “left” for the 1st and
5th answer of a question, “center” for the 2nd and 4 :sup:`th` answer
and “right” for the 3th answer of every question.

Example 3:

TypoScript: myVars.list.even\_odd = -odd,-even

Template ###TEMPLATE\_HIGHSCORE\_ENTRY###: <tr
class="###PREFIX###-tr###MY\_EVEN\_ODD###">

This means: ###MY\_EVEN\_ODD### will be replaced by “-odd” for odd
rows and “-even” for even rows.

Example 4:

TypoScript:myVars.answers.input\_radio = onclick="changeBG();"
style="float: right;"

The style- and onclick-Attribute will be added to every radio-button
of a question.

Example 5:

TypoScript:myVars.answers.input\_wrap = <label
for=”answer1”>\|</label>,<label for=”answer2”>\|</label>

You can wrap the text of radio-buttons and check-boxes.

Example 6:

TypoScript:myVars.answers.input\_id = 1andmyVars.answers.input\_label
= 1

Automatically wraps the radio-buttons and check-boxes with a label-
field and sets an ID for every input-field.With “input\_label = 2” the
label will wrap the input-field and the text!

Example 7:

TypoScript:myVars.answers.select= class=”select-
box”ormyVars.answers.input\_area = cols=”33” rows=”5”

All select-boxes of the quiz will get the class “select-box”; size for
textareas.

Example 8:

TypoScript:myVars.page.name= Test-quiz

You can use this variable in the page-template as ###MY\_NAME###.

