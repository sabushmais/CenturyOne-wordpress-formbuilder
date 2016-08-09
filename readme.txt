*** Standard light weight Form Builder ***
Contributors:
Donate link:
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl.html
Tags: simple,
Tested on: wordpress 4.5.3


A light and simple CenturyOne form builder plugin.

*** Description ***
This is a light weight, simple form builder to build as many standard forms as you want for your wordpress site.
The standard functionality allows you to receive the data filled by the user in an email address which you define during
the building of the specific form. It introduces two layers of data validation and verification conform to wordpress policy as follows:

* Data validation: All user inputs are checked for validation first.

* Data sanitizing: All user inputs are stripped to avoid inputting malicious data for cross-site scripting vulnerabilities.

There is very minimal setup to start using the plugin. You activate it then a 'Century Form Builder' menu appears in your
admin navbar. The page 'century Form Builder' allows you to specify the email address (to which the user-data will be sent),
and the names, types of the fields that the form needs to contain. Once the form created a shortcode is generated allowing
you to place the newly created form in any page you want. (only by copying the generated shortcode [century_form_X]
in the page and location that you want)

A standard set of inputs is made in your disposition : text fields, dates, email fields, number fields, text area field,
password field.

This plugin allows you to benefit from Two levels of security to ensure protection from csx as much as possible
though I do not claim any responsibility in case of occurrence of such attacks or any other security issues:

* Level 1: Data validation, When the user inputs the data in the form the validity of the input fields is checked
(By default field texts should be at least 4 characters long and password and emails at least 7 characters long), emails are
    checked for their validity
* Level 2: Data sanitizing, another level to ensure security is by sanitizing the user input data, after validation the data
is sanitized using wordpress functions to strip it from all potentially malicious forms leading to cross site scripting
as much as possible.

*** Why Choose This Plugin? ***
Before developing this plugin I researched the existing form building solutions online, and very few of them if not none
answered the need I had to submit the users received data to my email in a flexible natural way. So I made this simple
natural way to build forms in wordpress. Updates and amelioration of the plugin are to be expected But since the plugin
uses GPL2 and later license you are free to add, and advance the plugin's functionality or appearance as much as you see
fit in respect of gpl2 conditions. And if you are a fellow developer, please feel free to give your comments propositions
about this plugin.

Hopefully this plugin will fulfil all your needs, if not please contact me and I will customise the plugin to your exact requirements.

*** Installation ***
* You can either install this plugin directly from wordpress list of plugin or just downlod it, unzip it then place it in
the plugins folder of your wp-installation (your_wordpress_project_name/wp-content/plugins/)
* After installing the plugin you will need to activate it. At this moment a table that will save data about your
customized forms will be created in the database

*** How to Use ***
After activating the plugin, go to 'Century Form Builder' menu in the navbar of the admin menu.
This form allows you to determine the email you want the form data to be sent to, and to specify the field name and field
type of each element of your form.

After submitting the form, and if no error was found, a shordcode is generated. You need to copy this shortcode and place
it in the page you want while putting it between brackets, in this form [generated_shotcode].

*** About Me ***
I am a software engineer and also a software developer. I aim to develop software solutions to solve problems. I am familiar
with many programming languages and technologies but I currently work in php and web development.

*** Frequently Asked Questions ***
* How to display the contact form?
After creating the custom form using the centuryOne Form Builder, a shortcode (century_form_x) is generated.
You need to copy this short code and put it, between ([]) in the page you want to have this custom form.[century_form_x]

* I get an error saying that the email could not be sent?
If such message is shown. you need to contact your web host and ask him to allow sending emails from the server.

* I don't receive the email?
Please check the junk folder if you don't see the email in your mailbox

* Can I have multiple forms in the same page?
This version does not allow the use of multiple forms in the same page. But upcoming ones will.
