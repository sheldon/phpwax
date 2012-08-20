<?php
namespace Wax\Form\Widget;


/**
 * Country Select Widget class
 *
 * @package PHP-Wax
 **/
class CountrySelectInput extends SelectInput {
  
  public $choices = array("Afghanistan", "Albania", "Algeria", "American Samoa", "Andorra", "Angola", "Anguilla", 
  "Antarctica", "Antigua And Barbuda", "Argentina", "Armenia", "Aruba", "Australia", 
  "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", 
  "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegowina", 
  "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", 
  "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burma", "Burundi", "Cambodia", 
  "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", 
  "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", 
  "Comoros", "Congo", "Congo, the Democratic Republic of the", "Cook Islands", 
  "Costa Rica", "Cote d'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", 
  "Djibouti", "Dominica", "Dominican Republic", "East Timor", "Ecuador", "Egypt", 
  "El Salvador", "England", "Equatorial Guinea", "Eritrea", "Espana", "Estonia", 
  "Ethiopia", "Falkland Islands", "Faroe Islands", "Fiji", "Finland", "France", 
  "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", 
  "Georgia", "Germany", "Ghana", "Gibraltar", "Great Britain", "Greece", "Greenland", 
  "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guinea", "Guinea-Bissau", "Guyana", 
  "Haiti", "Heard and Mc Donald Islands", "Honduras", "Hong Kong", "Hungary", "Iceland", 
  "India", "Indonesia", "Ireland", "Israel", "Italy", "Iran", "Iraq", "Jamaica", "Japan", "Jordan", 
  "Kazakhstan", "Kenya", "Kiribati", "Korea, Republic of", "Korea (South)", "Kuwait", 
  "Kyrgyzstan", "Lao People's Democratic Republic", "Latvia", "Lebanon", "Lesotho", 
  "Liberia", "Liechtenstein", "Lithuania", "Luxembourg", "Macau", "Macedonia", 
  "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", 
  "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", 
  "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", 
  "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", 
  "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", 
  "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Ireland", 
  "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Panama", 
  "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", 
  "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russia", "Rwanda", 
  "Saint Kitts and Nevis", "Saint Lucia", "Saint Vincent and the Grenadines", 
  "Samoa (Indep}ent)", "San Marino", "Sao Tome and Principe", "Saudi Arabia", 
  "Scotland", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", 
  "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", 
  "South Georgia and the South Sandwich Islands", "South Korea", "Spain", "Sri Lanka", 
  "St. Helena", "St. Pierre and Miquelon", "Suriname", "Svalbard and Jan Mayen Islands", 
  "Swaziland", "Sweden", "Switzerland", "Taiwan", "Tajikistan", "Tanzania", "Thailand", 
  "Togo", "Tokelau", "Tonga", "Trinidad", "Trinidad and Tobago", "Tunisia", "Turkey", 
  "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", 
  "United Arab Emirates", "United Kingdom", "United States", 
  "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", 
  "Vatican City State (Holy See)", "Venezuela", "Viet Nam", "Virgin Islands (British)", 
  "Virgin Islands (U.S.)", "Wales", "Wallis and Futuna Islands", "Western Sahara", 
  "Yemen", "Zambia", "Zimbabwe");
  
  public function tag_content() {
    $output = "";
    $choice = '<option value="%s"%s>%s</option>';
    foreach($this->choices as $value=>$option) {
      $sel = "";
      if($this->value==$value) $sel = ' selected="selected"';
      $output .= sprintf($choice, $value, $sel, $option);
    }
    return $output;
  }
  



} // END class