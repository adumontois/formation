## Exercice for Erwan

# 0 - Build the full FrameWork from OCRoom : Done !

# 1 - Add Feature : Allow logged user to logout :
* Add a link in menu to logout the connected user. 
* Make a new action in ConnexionController.
* After logout, redirect to homepage. 

# 2 - Refactoring user managment :
* Store user in Database : Create table(s) according to DreamCentury database nomenclature.
* Add a new Manager to work the new table(s) (according to the Framework manager name nomenclature).
* Update your ConnexionController code.
* Adding a subscribtion form : Input list [ login, passwod, password confirmation, email, email confirmation ].

# 2.1 - Two types of user. 
* The users who subrscribe with subscription form are now simple writers. They are not a full administrator and they can only edit their news.
