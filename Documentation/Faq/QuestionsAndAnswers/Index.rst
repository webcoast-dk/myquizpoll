

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


Questions and answers
^^^^^^^^^^^^^^^^^^^^^

- I have enabled the basic poll but how can I display a graphical poll
  result?
  
  Please take a look at the example-file “template\_poll.html” or
  “template\_poll\_jquery.html” in the examples-folder. Otherwise take a
  look at this website: `http://www.ajaxschmiede.de/tools/diagramme-
  erstellen-leicht-gemacht-mit-der- <http://www.ajaxschmiede.de/tools
  /diagramme-erstellen-leicht-gemacht-mit-der-google-chart-api/>`_
  `google-chart-api/ <http://www.ajaxschmiede.de/tools/diagramme-
  erstellen-leicht-gemacht-mit-der-google-chart-api/>`_

- I have enabled the basic poll but the extension displays only one
  question. What should I do?
  
  The basic poll mode is really basic. It can handle only one question
  with radio-button or select-box. If you need an advanced poll please
  try the quiz-mode with advanced statistics.

- I can´t see any results from an user? Whats wrong?
  
  You have to enable the advanced statistics if you need detailed
  results.

- I have enabled advanced statistics but I don´t see any statistics in
  the front-end. What can I do?
  
  See “template\_statistics.html” in the examples-folder for more
  information. Try to use REF\_QR\_ANSWER\_ALL instead of
  REF\_QR\_ANSWER\_CORR in the template QUIZ\_ANSWERS.

- I am using categories but they are ignored in the front-end. Whats
  wrong?
  
  In some cases you must set the category for the first question. See TS
  “startCategory”. And: the categories must be in the same folder than
  the questions!

- I want to display all check-boxes and radio-buttons on the right side.
  How can I do that?
  
  The following TypoScript will help
  you:plugin.tx\_myquizpoll\_pi1.myVars.answers {input\_checkbox =
  style="float: right;"input\_radio = style="float:
  right;"}page.includeCSS.file5 =
  typo3conf/ext/myquizpoll/examples/right\_buttons.css

- I want to display the title or the category only once at a page!You
  can hide the following titles or categories with style-sheets. Use a
  code like this one:<div class=”mytile-###VAR\_QUESTION\_NUMBER###”>###
  VAR\_QUESTION\_TITLE###</div>Then hide all titles with NUMBER > 1 like
  this via CSS: .mytitle-2,.mytitle-3 { display: none; }

- The quiz taker should enter his name. “unknown” is a bad name. How can
  I change that?
  
  You can delete the default values like
  this:plugin.tx\_myquizpoll\_pi1.\_LOCAL\_LANG.de {no\_name =no\_email
  =no\_homepage =}And you can add this in the template-file to the
  JavaScript-function quizsubmit::feld = 'tx\_myquizpoll\_pi1[name]';if
  (quizform.elements[feld].value.length==0){window.alert('Please enter
  your name!');return false;}

- My TypoScript values are not accepted? Whats wrong?
  
  Note that the following values will be overwritten for compatibility
  reasons with old values (if they exist):'userData.askAtQuestion' with
  'dontShowUserData', 'highscore.entries' with 'highscoreEntries',
  'highscore.sortBy' with 'sortHighscoreBy', 'highscore.showAtFinal'
  with 'showHighscore' and 'highscore.dateFormat' with 'dateFormat'.

- I need more than 6 answers for one question. What should I do?
  
  You can use the “copy & paste” method to add new fields. Open this
  files: ext\_tables.sql, ext\_tables.php and locallang\_db.xml. Search
  for the following database-fields and copy & paste them (table
  tx\_myquizpoll\_question):answer1, correct1, points1, joker1\_1,
  joker2\_1, category1Replace the 1 at the end with 7,8 and so on...If
  you have enabled the advanced statistics then copy & paste this field
  too (table tx\_myquizpoll\_relation): checked1.Click in the extension
  manager at this extension and create the new database fields.Finally
  set the TS variable “answerChoiceMax = 7” (8,9 or so on).Note: only up
  to 12 answers are supported in the backend when viewing statistics.
  And you need to make this steps again after an myquizpoll-update!Note:
  you find and older version (1.2.15) with up to 12 answers on my
  homepage: `http://www.quizpalme.de/myquizandpoll/download.html
  <http://www.quizpalme.de/myquizandpoll/download.html>`_ Furthermore
  you find an extension that extends myquizpoll with 6 more questions:
  `http://www.quizpalme.de/myquizandpoll/download/myquizpoll-addons.html
  <http://www.quizpalme.de/myquizandpoll/download/myquizpoll-
  addons.html>`_

- I want 2 questions on the first page, then 4, then 1 and so on. What
  can I do?
  
  You can use categories. Define a category for every page. Set the
  startCategory for the first question(s). Set a category for each
  question and set the next category for each question (the category of
  the next page). Set pageQuestions = 100.

- I need less than 6 answers for one question. What should I do?
  
  First, set “answerChoiceMax=4” if you need only 4 answers. Second, you
  can hide answers if you set this
  TSconfig:TCEFORM.tx\_myquizpoll\_question.answer5.disabled =
  1TCEFORM.tx\_myquizpoll\_question.correct5.disabled =
  1TCEFORM.tx\_myquizpoll\_question.points5.disabled =
  1TCEFORM.tx\_myquizpoll\_question.joker1\_5.disabled =
  1TCEFORM.tx\_myquizpoll\_question.joker2\_5.disabled =
  1TCEFORM.tx\_myquizpoll\_question.category5.disabled = 1Note: you find
  the TSconfig-field here: Page properties → Options or Resources.

- I need larger answers with more than 255 characters. Textarea not
  input-field!
  
  You find an older version (1.2.16) with this possibilities on my
  homepage. Link see above.Furthermore you will find there an extension
  that extends myquizpoll.

- I want 2 different explanations. One for correct answered questions
  and one for false answered questions!
  
  You find an older version (1.4.2) and an add-on-extension with this
  possibilities on my homepage too. Link see above.

- I want explanations for every answer of a question!
  
  You find an add-on extension with this possibilities on my homepage.
  Link see above. In order to hide the normal explanation field enter
  this at the Page TSconfig-field:
  TCEFORM.tx\_myquizpoll\_question.explanation.disabled = 1

- How can I hide fields in the backend?
  
  See above. Its easy:
  TCEFORM.tx\_myquizpoll\_question.filed\_to\_hide.disabled = 1 in the
  TSconfig.You will find the field-names in the file “ext\_tables.sql”.

- Paragraphs from the RTE area are not displayed/shown in the frontend.
  Why not?
  
  Please use breaks (<Shift>+<Enter>) instead of paragraphs. Otherwise
  allow them with this TS:plugin.tx\_myquizpoll\_pi1 { general\_stdWrap
  > general\_stdWrap {  parseFunc < tt\_content.text.20.parseFunc }}Then
  clear all chaches.

- Users should decide if they want to be displayed in the highscore
  list. Is there a way to do that?
  
  It is possible. Take a look at the “template\_statistics.html” if you
  want to know how you can do this.

- The name of a quiz taker should link to a profile page...
  
  This works if users are logged in. highscore.linkTo =
  id=54&feuid=0&myParam=myVal is an example. The first parameter of
  linkTo specifies the destination of the link. Use only numbers! The
  second parameter can be set to everything. The value of this parameter
  will be replaced with the fe\_users id. All other parameters are
  optional. ###VAR\_NAME### in ###TEMPLATE\_HIGHSCORE\_QUIZ\_TAKER###
  will contain the generated link.

- How can I display all correct answers of a question in a cooler
  way?Take a look at the karussell-extension: template\_ccc\_quiz.html
  is a cool example.

- What about an explanation of the markers?
  
  You will find all supported marker in the template
  'pi1/tx\_myquizpoll\_pi1.tmpl'. Some of them are explained there.

- I need a reload-lock. What can I do?
  
  Take a look at the TS-variable “doubleEntryCheck” or
  “useCookiesInDays”.

- I need help or I need this and that! What can I do?
  
  Take a look at the this good extension: pbsurvey. Or you can write me
  an email: `http://www.quizpalme.de/kontakt.html
  <http://www.quizpalme.de/kontakt.html>`_

