=== WordPress Petfinder Listing ===
Contributors: chows305
Tags: petfinder, adoptable pets
Requires at least: 3.5
Tested up to: 4.0
Stable tag: 1.02
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html




== Description ==

= Offering a filterable and searchable list of all animals at your shelter with no coding needed. =

WordPress Petfinder Listing offers a filterable and searchable list all of your animals that you have uploaded to your Petfinder account. Users can search by animal’s name, or check off their preferences for what type of pet they are looking for.  Each pet can be clicked on to display more information.  Customize colors, list adoption fees, and link to your adoption form.


= Features =

1. Filterable - Filter by Type, Age, Size, Gender, Temperament, Special Needs, and Breeds
2. Searchable - List is narrowed with every letter you type into search bar
3. Click On Any Pet - to view more information and photos
4. List Your Adoption Info - such as fees, specials, and what to expect when you adopt. 
5. Colors - are completely customizable to match your brand.
6. Widget - Place in sidebar and and link to go back to your adoptable pets.  Customize color and text.

= Demo =

http://www.lendingapaw.org/petfinder-search-rescue/


= Shortcode =

[petfinder_search_rescue api_key="YOUR API KEY" shelter_id="YOUR SHELTER ID" count="100"] Your Shelter ID is your Petfinder username. Should be your state abbreviation + 3 digits.

Replace “100″ with the number of animals you would like to display.
*Keep in mind the higher the number, the higher the load time. 

You can generate your free API Key when you log into your Petfinder account here:
https://www.petfinder.com/developers/api-key

= Customize Settings =
Under the Settings menu, click on Petfinder:Search and Rescue
1. Customize colors- Choose the colors from the color picker 
2. Type in the link to your adoption form.  You must type in the full url
  	(Example - http://www.mywebsite.com/myadoptionform/)
3. Type in information about the adoption process in the textarea.  Here you can list adoption fees, specials, and what one can expect after adopting. 
4. After filling out all the fields click save

= Widget =

The widget displays a different photo of an animal from your shelter on every page.  Link to your adoption page and customize the look of the button.

1. Under the Appearance menu, click on Widgets.  
2. Drag WordPress Petfinder Listing Widget into your sidebar.  Type in your shelter id, api key, and shelter name.  
3. For the button, type in your text and choose a color.  
4. Type in the full link to your adoption page.  			
	(Example - http://www.mywebsite.com/adoptable-pets)


== Screenshots ==

1. Default view of plugin
2. Filtered animals by clicked prefrences
3. Filtered animals by searching by name
4. Clicked on a pet
5. Adoption Info
6. Custom colors
7. Settings page
8. Widget Examples
9. Preloader


== Installation ==

1. Upload expanded petfinder-search-rescue folder to the /wp-content/plugins/ directory
2. Activate the plugin through the "Plugins" menu in WordPress
3. Type in this shortcode to the page you want the plugin to display:
[petfinder_search_rescue api_key="YOUR API KEY" shelter_id="YOUR SHELTER ID" count="100"] 
4.  Replace "YOUR_API_KEY", 'YOUR_SHELTER_ID" with your Shelter's information. 
	-You will need to generate a free Petfinder API key on Petfinder here: 		http://www.petfinder.com/developers/api-key. 
	-Your shelter ID is your Petfinder username, usually your state abbreviation plus 		3 digits.
5. Replace count with the number of animals you wish to display (more animals means more load time)
6. Go to Settings -> WordPress Petfinder Listing to customize colors, add link to adoption form, and add information about adopting at your shelter.
7. Go to Appearance -> Widgets to add the WordPress Petfinder Listing widget and link back to your adoptable pets (not required)

== Frequently Asked Questions ==

= Why does the sidebar widget sometimes only show a paw print? =
The widget pulls only large and medium sized photos that will show nicely in your sidebar. If your pet does not have those photo sizes, this placeholder will take place instead.

= Where can I get my API key for Petfinder? =
http://www.petfinder.com/developers/api-key.

= What to do if your page says "Petfinder is down for the moment. Please check back shortly." =
Do just that.  Petfinder is usually only down for a few seconds.

== Upgrade Notice == 
Please make sure you upgrade your Wordpress to the latest version.  You will not be able to customize your colors on the settings page if you are not up to at least version 3.5

== Changelog ==

= 1.0 =
First version
= 1.02 =
-Added option to set options to hide on default or to be removed altogether
-Fixed styling issue on background and icons
-Hides pet from view if does not have featured image attached