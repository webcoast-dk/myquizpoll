

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


Table of all variables (\*: if you include the defaultSettings)
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. ### BEGIN~OF~TABLE ###

.. container:: table-row

   Property
         Property:

   Data type
         Data type:

   Description
         Description:

   Default\*
         Default:\*


.. container:: table-row

   Property
         isPoll

   Data type
         Int [0-1]

   Description
         Quiz or poll? 0: normal mode (quiz or poll); 1: basic poll mode (see
         chapter “The basic poll mode”)

   Default\*
         0


.. container:: table-row

   Property
         tableAnswers

   Data type
         string

   Description
         In the basic poll mode you can decide between 2 database tables for
         the poll results: “tx\_myquizpoll\_result” and
         “tx\_myquizpoll\_voting”. The last one is optimized for polls and will
         be automatically used if you use the jQuery submit.

   Default\*
         tx\_myquizpoll\_result


.. container:: table-row

   Property
         sortBy

   Data type
         string

   Description
         Sort questions by: sorting \| uid \| title \| random

   Default\*
         sorting


.. container:: table-row

   Property
         pollStart

   Data type
         Int

   Description
         In poll and result list mode: show which question? 0: first; 1:
         second; 2: third...

   Default\*
         0


.. container:: table-row

   Property
         mixAnswers

   Data type
         Int [0-1]

   Description
         Mix/shuffle/random selectable answers at the “select”-page? 0: no; 1:
         yes.

   Default\*
         0


.. container:: table-row

   Property
         answerChoiceMax

   Data type
         Int [1-12]

   Description
         Maximum answers choice able per question. See chapter “FAQ”

   Default\*
         6


.. container:: table-row

   Property
         allowSkipping

   Data type
         Int [0-1]

   Description
         Questions can be put back? 0: no; 1: yes, questions can be skipped

   Default\*
         0


.. container:: table-row

   Property
         pageQuestions

   Data type
         Int [0-100]

   Description
         No. of questions / page: 0..1000 (0: all questtions at the first page)

   Default\*
         0


.. container:: table-row

   Property
         noNegativePoints

   Data type
         Int [0-4]

   Description
         Allow negative scores? 0: yes; 1: no, the minimum value per question
         is 0 scores; 2: ignore false answers = no negative scores at all; 3:
         every answer must be correct, otherwise the quiz taker will get 0
         scores; 4: 0 scores if every answer is correct, else x scores
         (x=scores of the question)

   Default\*
         0


.. container:: table-row

   Property
         dontShowPoints

   Data type
         Int [0-1]

   Description
         Disable scores: 0: show scores per question; 1: don't show any scores.

         Note: 1 disables every scores-handling! No scores-calculations are
         made.

   Default\*
         0


.. container:: table-row

   Property
         noAnswer

   Data type
         Int [0-1]

   Description
         What about questions which have not been answered by the quiz taker?
         0: ignore them; 1: that are false answers (old default setting)

   Default\*
         0

.. container:: table-row

   Property
         votedOnly

   Data type
         Int [0-1]

   Description
        Poll mode: what about answers with no votes? 0: show them too in the result; 1 ignore them in the result

   Default\*
         0

.. container:: table-row

   Property
         enforceSelection

   Data type
         Int [0-1]

   Description
         Enforce selection: 0: no; 1: yes, the user must select or insert a
         answer

   Default\*
         0


.. container:: table-row

   Property
         showAnswersSeparate

   Data type


   Description
         Do you want to show the answers on a separate page? 0: answer(s) +
         next question(s) on one site; 1: answer(s) + next question(s) on
         separate sites. Restriction: pageQuestions>0

   Default\*
         0


.. container:: table-row

   Property
         dontShowCorrectAnswers

   Data type
         Int [0-1]

   Description
         Don't show correct answers after submit: 0: show correct answers; 1:
         don't show questions again

   Default\*
         0


.. container:: table-row

   Property
         showAllCorrectAnswers

   Data type
         Int [0-3]

   Description
         Do you want to show the correct answers only on the final page? If you
         use a multi-page-quiz you can enable this entry and disable the
         dontShowCorrectAnswers-entry: 0: nothing happens; 1: show all correct
         answers on the last page (no questions left); 2: show only the correct
         answered questions; 3: show only the false answered
         questions.Restriction: pageQuestions>0 (but not for emails).

   Default\*
         0


.. container:: table-row

   Property
         showDetailAnswers

   Data type
         Int [0-1]

   Description
         Show details for questions with text-answers when showing all answers?
         0: no; 1: yes, show a details link and after clicking on it show all
         text-answers. advancedStatistics must be enabled! xAjax required!

   Default\*
         0


.. container:: table-row

   Property
         starRatingDetails

   Data type
         Int [0-1]

   Description
         Show details for questions with star-answers when showing all answers?
         0: no; 1: yes, but jQuery is required! Furthermore the template file
         must be defined at your homepage via TypoScript.

   Default\*
         0


.. container:: table-row

   Property
         showAnalysis

   Data type
         Int /string

   Description
         Show a final page which depends on reached percentage? 0: no extra
         content on the final page. Else: show a page or template which depends
         on reached percentage. See chapter “Dynamic Template Scopes”.

   Default\*
         0


.. container:: table-row

   Property
         showEvaluation

   Data type
         Int / string

   Description
         Show a final page which depends on reached scores? 0: no extra content
         on the final page. Else: show a content element or template which
         depends on reached scores. See chapter “Dynamic Template Scopes”.

   Default\*
         0


.. container:: table-row

   Property
         showCategoryElement

   Data type
         Int [0-4]

   Description
         Show a final page which depends on categories? 0: no extra content on
         the final page. 1: show the content element of the last category. 2:
         show the content element of the category with the most occurrence. 3:
         like 2, but show the content of all “most categories”. 4: show the
         content elements of all - by the user - used categories. Restriction
         for 2,3 and 4: advanced statistics enabled and template
         ###TEMPLATE\_CATEGORY\_ELEMENT### present.

   Default\*
         0


.. container:: table-row

   Property
         userData.askAtStart

   Data type
         Int [0-1]

   Description
         Ask for user data (name and so on) at a special start page? 0: no; 1:
         yes

   Default\*
         0


.. container:: table-row

   Property
         userData.askAtQuestion

   Data type
         Int [0-2]

   Description
         Ask for the user data at the question-page? 0: no; 1: yes; 2: ves, but
         only once

   Default\*
         1


.. container:: table-row

   Property
         userData.askAtFinal

   Data type
         Int [0-1]

   Description
         Ask for the user data at the final page? 0: no; 1: yes

   Default\*
         0


.. container:: table-row

   Property
         userData.showAtAnswer

   Data type
         Int [0-1]

   Description
         Show the submitted user data at the answers-page? 0: no; 1: yes

   Default\*
         0


.. container:: table-row

   Property
         userData.showAtFinal

   Data type
         Int [0-1]

   Description
         Show the submitted user data at the final page? 0: no; 1: yes

   Default\*
         0


.. container:: table-row

   Property
         userData.tt\_address\_pid

   Data type
         Int

   Description
         PID where an address should be saved into (tt\_address-folder), See
         template template\_address.html. Note: you need an additional
         extension for this feature.

   Default\*
         none

.. container:: table-row

   Property
         userData.tt\_address\_groups

   Data type
         string

   Description
         tt\_address-groups (uid´s) of the new address.

   Default\*
         none

.. container:: table-row

   Property
         email.admin\_mail

   Data type
         string

   Description
         Email address of the admin

   Default\*
         none

.. container:: table-row

   Property
         email.admin\_name

   Data type
         string

   Description
         Name of the admin

   Default\*
         none

.. container:: table-row

   Property
         email.admin\_subject

   Data type
         string

   Description
         Subject of the email to the admin

   Default\*
         none

.. container:: table-row

   Property
         email.user\_subject

   Data type
         string

   Description
         Subject of the email to the quiz taker

   Default\*
         none

.. container:: table-row

   Property
         email.from\_mail

   Data type
         string

   Description
         Email address of the sender

   Default\*
         none

.. container:: table-row

   Property
         email.from\_name

   Data type
         string

   Description
         Name of the sender

   Default\*
         none

.. container:: table-row

   Property
         email.send\_admin

   Data type
         Int [0-2]

   Description
         Send an email to the admin? 0: no; 1: yes, on the final page; 2: yes,
         after the final page (highscore list, when userData.askAtFinal=1)

   Default\*
         0


.. container:: table-row

   Property
         email.send\_user

   Data type
         Int [0-2]

   Description
         Send an email to the quiz taker? 0: no; 1: yes, on the final page; 2:
         yes, after the final page (highscore list, when userData.askAtFinal=1)

   Default\*
         0

.. container:: table-row

   Property
         email.answers

   Data type
         string

   Description
         Send an email on specific answer? Can be set as an JSON-object:

         {"question_uid":{"number_of_answer":{"email":"E-mail","name":"Name","subject":"Subject","template":"Template_postfix_name"},"number_of_answer":{"email":"E-mail","name":"Name","subject":"Subject","template":"Template_postfix_name"}}}

         "email" is mandatory, "subject" is mandatory, if "template" is missing, default template below is used, if "template":"myquizpoll", template ###TEMPLATE_ANSWER_EMAIL_MYQUIZPOLL### is used. Take care to use correct quotes (")!

         Example question: "Frage 1: Lieblingsfarbe" (question_id in the DB: 12), Answer 1: blau, Answer 2: grün, Answer 3: rot

         Example JSON-object: {"12":{"2":{"email":"green@test.de","name":"Grün","subject":"Lieblingsfarbe ist grün!","template":"gruen"},"3":{"email":"red@test.de","name":"Rot","subject":"Lieblingsfarbe ist rot!"}}}

         The template names would be: ###TEMPLATE_ANSWER_EMAIL_GRUEN### and the default template for Rot.

         You can extend the object for every answer: {"12":{"2":{…},"3":{…}},13:{"1":{…},"5":{…}}}

         Note: this works only if "advancedStatistics = 1".

   Default\*
         none

.. container:: table-row

   Property
         cancelWhenWrong

   Data type
         Int [0-1]

   Description
         Cancel the quiz when the user gives a wrong answer? 0: don't cancel a
         quiz prematurely; 1: show the template TEMPLATE\_QUIZ\_END when the
         quiz taker gives a wrong answer.

   Default\*
         0


.. container:: table-row

   Property
         finalWhenCancel

   Data type
         Int [0-1]

   Description
         Show the final page too when a quiz was canceled (time over or
         cancelWhenWrong=1)? 0: no, only the template TEMPLATE\_QUIZ\_END; 1:
         yes, TEMPLATE\_QUIZ\_END and TEMPLATE\_QUIZ\_FINAL\_PAGE.

   Default\*
         0


.. container:: table-row

   Property
         finishedMinPercent

   Data type
         int / string

   Description
         Percentage of questions that must be answered correctly. Integer
         value: 0: don't cancel a quiz prematurely;  *int* or  *int1* : *int2*
         : show the page with the UID  *int2* or the template
         TEMPLATE\_QUIZ\_FINISHEDMINPERCENT after reaching finishedMinPercent
         percent ( *int* or  *int1* ) and stop the quiz.

   Default\*
         0


.. container:: table-row

   Property
         finishAfterQuestions

   Data type
         Int [>=0]

   Description
         Finish after X questions? 0: no, show all questions; >1: regular
         finish after “finishAfterQuestions” questions. This works only then
         good if you set “userSession=1”. Usefull for tests and if you set
         “sortBy=random”.

   Default\*
         0


.. container:: table-row

   Property
         pageTimeSeconds

   Data type
         Int [>=0]

   Description
         Limited time per page. Integer value: 0: no limitation; >0: send the
         form automatically after “pageTimeSeconds” :underline:`seconds` to the
         server

   Default\*
         0


.. container:: table-row

   Property
         quizTimeMinutes

   Data type
         Int [>=0]

   Description
         Limited time per whole quiz. Integer value: 0: no limitation; >0:
         cancel the quiz after “quizTimeMinutes” :underline:`minutes`

   Default\*
         0


.. container:: table-row

   Property
         useJokers

   Data type
         Int [0-1]

   Description
         Enable the 3 available jokers? 0: no; 1: yes, show 3 different jokers

         Restriction: pageQuestions = 1.

         :underline:`Requirements` : xajax-Extension must be installed.

   Default\*
         0


.. container:: table-row

   Property
         jokers.unlimited

   Data type
         Int [0-1]

   Description
         Unlimited jokers? 0: no; 1: yes

   Default\*
         0


.. container:: table-row

   Property
         jokers.halvePoints

   Data type
         Int [0-1]

   Description
         Give only halve scores when using a joker? 0: no; 1: yes

   Default\*
         0


.. container:: table-row

   Property
         **advancedStatistics**

   Data type
         Int [0-1]

   Description
         Save more data into other database-tables? 0: no, I don´t need
         enhanced statistics; 1: yes, save everything in the database, because
         I want to see enhanced statistics. Note: this generates a database-
         entry for each answered question! Read the FAQ for more infos.

   Default\*
         0


.. container:: table-row

   Property
         **userSession**

   Data type
         Int [0-1]

   Description
         Enable user session (Cookies)?: 0: no; 1: yes, I want to avoid
         database-accesses (recommended!). Note: userSession ist required for
         some features.

   Default\*
         1


.. container:: table-row

   Property
         requireSession

   Data type
         Int [0-1]

   Description
         Require user session (cookies)?: 0: no; 1: yes, improve security and
         require cookies (some actions will not work without a valid cookie).

   Default\*
         0


.. container:: table-row

   Property
         allowBack

   Data type
         Int [0-1]

   Description
         Show a back-button when pageQuestions>1? 0: no; 1: yes. Note: only the
         advanced statistics will be updatet in back mode!

   Default\*
         0


.. container:: table-row

   Property
         highscore.entries

   Data type
         Int [>=0]

   Description
         Max. number of entries in the highscore list. If you don´t import the
         defaultSettings the default value is 0 else the default value is 10.0:
         show all entries; x>0: show Top X entries, e.g. Top 10.

   Default\*
         10


.. container:: table-row

   Property
         highscore.sortBy

   Data type
         string

   Description
         Sort highscore list by: points: reached scores; percent: percent of
         answered question; o\_percent: percent of all questions; time: end
         time - start time; date: creation date; lastcat: last categorie;
         nextcat:next categorie.

   Default\*
         points


.. container:: table-row

   Property
         highscore.groupBy

   Data type
         string

   Description
         Group the highscore by something? name: quiz taker name; fe\_uid:
         front-end-user.

   Default\*
         none

.. container:: table-row

   Property
         Highscore.showUser

   Data type
         Int [0-1]

   Description
         Show the highscore only of the logged-in user? 0: no; 1: yes.

   Default\*
         0


.. container:: table-row

   Property
         Highscore.ignorePid

   Data type
         Int [0-1]

   Description
         Ignore the PID and display a highscore of all folders? 0: no; 1:yes.

   Default\*
         0


.. container:: table-row

   Property
         highscore.showAtFinal

   Data type
         Int [0-1]

   Description
         Show highscore at the final page: 0: no; 1: yes

   Default\*
         0


.. container:: table-row

   Property
         highscore.linkTo

   Data type
         string

   Description
         If front-end-users (fe\_users) take the quiz you can generate a link
         to a profile-page of that user. See FAQ for more information.

   Default\*
         none

.. container:: table-row

   Property
         highscore.dateFormat

   Data type
         string

   Description
         Date format in the highscore list

   Default\*
         m-d-Y


.. container:: table-row

   Property
         loggedInCheck

   Data type
         Int [0-1]

   Description
         Must users be logged in? 0: no; 1: yes

   Default\*
         0


.. container:: table-row

   Property
         loggedInMode

   Data type
         Int [0-1]

   Description
         Check for double entries when logged in? 0: no; 1: yes, logged in user
         can take a quiz/poll only once. Doesn't work if you use the table
         “tx\_myquizpoll\_voting”.

   Default\*
         0


.. container:: table-row

   Property
         fe\_usersName

   Data type
         string

   Description
         fe\_users-field for the quiz taker name, e.g. name or username

   Default\*
         name


.. container:: table-row

   Property
         doubleEntryCheck

   Data type
         Int [>=0]

   Description
         If this property is set to X>0, the same quiz or poll cannot be taken
         twice with the same ip-address X days long. If > 1, then the user is
         locked for “doubleEntryCheck” :underline:`days` (in version 0.2.0:
         seconds)

   Default\*
         0


.. container:: table-row

   Property
         doubleCheckMode

   Data type
         Int [0-1]

   Description
         Double entry check mode: 0: show an error message at the second visit;
         1: continue the quiz (where it was stopped last time) at the second
         visit

   Default\*
         0


.. container:: table-row

   Property
         useCookiesInDays

   Data type
         Int [>=-1]

   Description
         Use cookies to remember the quiz takers UID? If yes, the quiz taker
         can continue later with the quiz. When done, a quiz cannot be taken
         twice! Specifies the lifetime of a cookie in :underline:`days` . -1
         means: session cookie

   Default\*
         0


.. container:: table-row

   Property
         cookieMode

   Data type
         Int [0-4]

   Description
         Cookie mode: generate the name of the cookie with... 0: normal (use
         the results-PID); 1: combine the PID with the language-id; 2: combine
         the PID with the fe\_users-id; 3: combine the PID with the questions-
         PID; 4: combine the PID with onlyCategories; 5 combine the PID with
         the fe\_users-id and the newest question-UID (only for the poll-mode)

   Default\*
         0


.. container:: table-row

   Property
         allowCookieReset

   Data type
         Int [0-1]

   Description
         If useCookiesInDays<>0: allow users to delete/reset the cookie? 0: no;
         1: yes. You need to insert ###RESET\_COOKIE### in your template too.

   Default\*
         0


.. container:: table-row

   Property
         secondPollMode

   Data type
         Int [0-1]

   Description
         If check for double entry is on and isPoll=1. What show at the second
         visit? 0: start page with error message; 1: poll result list

   Default\*
         0


.. container:: table-row

   Property
         deleteResults

   Data type
         Int [0-3]

   Description
         Delete current user result data and entries older than one day at the
         end of the quiz? 0: no; 1: yes; 2: yes, but delete only entries with
         no fe\_users-ID (not logged in users); 3: yes, but delete only entries
         with an unknown name. Note: works only if a quiz is not canceled!

   Default\*
         0


.. container:: table-row

   Property
         deleteDouble

   Data type
         Int [0-1]

   Description
         If deleteResults>1: delete double entries too? 0: no; 1: yes, delete
         entries with the same name or fe\_uid and smaller percent values. This
         works only with entries that are stored with the myquizpoll version
         1.5.3 or greater!

   Default\*
         0


.. container:: table-row

   Property
         disableIp

   Data type
         Int [0-1]

   Description
         Disable the reading of the IP-address of the quiz-taker? 0: no; 1: yes

   Default\*
         0


.. container:: table-row

   Property
         remoteIP

   Data type
         Int [0-1]

   Description
         Try to get the real IP-address or take only the REMOTE\_ADDR? 0: real
         IP; 1: REMOTE\_ADDR

   Default\*
         0


.. container:: table-row

   Property
         blockIP

   Data type
         string

   Description
         Block some IP-addresses (e.g. from google) on the submit page?
         Expamples: 66.249. (to block all IPs that start with 66.249.);
         66.249.,127.0.0.1

   Default\*
         none

.. container:: table-row

   Property
         hideByDefault

   Data type
         Int [0-1]

   Description
         Hide user answers by default? 0: no; 1: yes, hide user result

   Default\*
         0


.. container:: table-row

   Property
         enableCaptcha

   Data type
         Int [0-1]

   Description
         If you have installed the extension 'sr\_freecap' you can add a
         captcha to the user data by enabling it here: 0: no captcha; 1: show
         the captcha near the user data (user data must be enabled)

   Default\*
         0


.. container:: table-row

   Property
         rating.extKey

   Data type
         string

   Description
         If “isPoll=1” you can rate pages. ExtKey and parameter are page-
         parameters. For an example take a look at the
         “template\_rating\_jquery.html”

   Default\*
         none

.. container:: table-row

   Property
         rating.parameter

   Data type
         string

   Description
         See above

   Default\*
         none

.. container:: table-row

   Property
         images.maxW and

         images.maxH

   Data type
         int

   Description
         Maximum width and height for images

   Default\*
         none

.. container:: table-row

   Property
         myVars

   Data type
         Array of strings

   Description
         Your own variables for all kind of lists: questions, answers and
         highscore list. See chapter “Your own variables”. Reserved variables
         are below...

   Default\*
         Some examples


.. container:: table-row

   Property
         .separator

   Data type
         char

   Description
         Separator in the text of your private variables

   Default\*
         ,


.. container:: table-row

   Property
         .answers.input\_id

   Data type
         Int [0-1]

   Description
         0: nothing; 1: give the input-fields and other fields a unique ID

   Default\*
         none

.. container:: table-row

   Property
         .answers.input\_label

   Data type
         Int [0-4]

   Description
         Wrap the answer-text and the input-field with a label? 0: no; 1: yes,
         but only the text of an answer; 2: yes, wrap the text and the input-
         field;3: like 2, but adds a class for each input-type too; 4: like 3,
         buts adds two classes (one for inline elements, e.g. class=”radio
         inline”); 5: like 3, but for Bootstrap 3; 6: like 4, but for Bootstrap
         3. Note: works only with radio-buttons and check-boxes!

   Default\*
         none

.. container:: table-row

   Property
         .answers.input\_radio

   Data type
         string

   Description
         Additional things for radio-buttons

   Default\*
         none

.. container:: table-row

   Property
         .answers.input\_checkbox

   Data type
         string

   Description
         Additional things for check-boxes

   Default\*
         none

.. container:: table-row

   Property
         .answers.input\_text

   Data type
         string

   Description
         Additional things for input-fields with type=”text”

   Default\*
         none

.. container:: table-row

   Property
         .answers.input\_area

   Data type
         string

   Description
         Additional things for textareas

   Default\*
         none

.. container:: table-row

   Property
         .answers.input\_wrap

   Data type
         string

   Description
         Possibility to wrap radio-buttons and check-boxes

   Default\*
         none

.. container:: table-row

   Property
         .answers.select

   Data type
         string

   Description
         Additional things for select-boxes

   Default\*
         none

.. container:: table-row

   Property
         .answers.option

   Data type
         string

   Description
         Additional things for option-fields

   Default\*
         none

.. container:: table-row

   Property
         quizName

   Data type
         string

   Description
         String for the marker ###QUIZ\_NAME###

   Default\*
         First page title


.. container:: table-row

   Property
         debug

   Data type
         Int [0-3]

   Description
         Debug mode? 0: no; 1, 2, 3: yes, but you will need a devlog-extension
         (e.g. devlog) too!

   Default\*
         0


.. container:: table-row

   Property
         startCategory

   Data type
         int

   Description
         UID of a category. First question must have this category. Following
         question depends on the category of the last question.

   Default\*
         none

.. container:: table-row

   Property
         onlyCategories

   Data type
         Int / string

   Description
         UIDs of all categories that should be shown. Can be used instead of
         startCategory. Categories of the answers will be ignored with this
         option!

   Default\*
         none

.. container:: table-row

   Property
         randomCategories

   Data type
         Int [0,1]

   Description
         Try to show at every page questions with different categories? 0: no;
         1: yesNote: this works only if you set “sortBY = random” too.

   Default\*
         0


.. container:: table-row

   Property
         ignoreSubmits

   Data type
         Int [0,1]

   Description
         Ignore all submits? 0: no; 1: yes. Useful, if you want to display only
         a result list.

   Default\*
         0


.. container:: table-row

   Property
         sysPID

   Data type
         Int + ,

   Description
         IDs of sysfolders with the questions (this is the startingpoint)

   Default\*
         none

.. container:: table-row

   Property
         resultsPID

   Data type
         int

   Description
         ID of the sysfolder where to store the results. Empty = sysPID.

   Default\*
         none

.. container:: table-row

   Property
         nextPID

   Data type
         int

   Description
         ID for form-URLs. This is the ID for the next pages. Leave it empty,
         if sysPID=nextPID.

   Default\*
         none

.. container:: table-row

   Property
         finalPID

   Data type
         int

   Description
         ID of the final page (if pageQuestions>1). Leave it empty, if
         sysPID=finalPID. Hint: if you want to make a redirect to a new page
         without any form-parameters, you can use “showAnalysis = 100:123” (123
         is the ID of your destination page).

   Default\*
         none

.. container:: table-row

   Property
         listPID

   Data type
         int

   Description
         ID of the page where to show the highscore list or the poll result

   Default\*
         none

.. container:: table-row

   Property
         startPID

   Data type
         Int

   Description
         ID of the first page; needed for the restart link. Note: since version
         1.5.8 this ID will taken from the DB, but you can override it with
         this value.

   Default\*
         none

.. container:: table-row

   Property
         templateFile

   Data type
         string

   Description
         Path and file name of your own HTML template

   Default\*
         EXT:myquizpoll/ pi1/tx\_myquizpoll\_pi1.tmpl


.. container:: table-row

   Property
         general\_stdWrap

   Data type
         stdWrap

   Description
         TS wrapping possibilities for questions, answers and explanations...

   Default\*
         none

.. container:: table-row

   Property
         general\_stdWrap.notForAnswers

   Data type
         Int [0-1]

   Description
         Use stdWrap for answers too? 0: yes; 1: no

   Default\*
         1


.. container:: table-row

   Property
         parseFunc

   Data type
         parseFunc

   Description
         Parsing of RTE-fields. See above...

   Default\*
         ...


.. container:: table-row

   Property
         CMD

   Data type
         string

   Description
         Allowed values: empty: normal mode; “score”: show the highscore list;
         “list”: show the poll result; “allanswers”: shows all questions and
         (correct) answers (see template TEMPLATE\_ALLANSWERS). “archive” shows
         you a list of old polls (only in the basic poll mode).

   Default\*
         none

.. ###### END~OF~TABLE ######

\*: none = no default value