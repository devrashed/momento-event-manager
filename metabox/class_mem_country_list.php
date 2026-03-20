<?php
/**
 *
 *  country list Hook
 *
 **/
namespace Wpcraft\Metabox;

class class_mem_country_list {

    public function wtmem_get_country_list() {
        return [
            "Afghanistan",
            "Albania",
            "Algeria",
            "American Samoa",
            "Andorra",
            "Angola",
            "Anguilla",
            "Antigua and Barbuda",
            "Argentina",
            "Armenia",
            "Aruba",
            "Australia",
            "Austria",
            "Azerbaijan",
            "Bahamas",
            "Bahrain",
            "Bangladesh",
            "Barbados",
            "Belarus",
            "Belgium",
            "Belize",
            "Benin",
            "Bermuda",
            "Bhutan",
            "Bolivia",
            "Bosnia and Herzegovina",
            "Botswana",
            "Brazil",
            "Brunei",
            "Bulgaria",
            "Burkina Faso",
            "Burundi",
            "Cabo Verde",
            "Cambodia",
            "Cameroon",
            "Canada",
            "Cayman Islands",
            "Central African Republic",
            "Chad",
            "Chile",
            "China",
            "Colombia",
            "Comoros",
            "Congo (Congo-Brazzaville)",
            "Cook Islands",
            "Costa Rica",
            "Croatia",
            "Cuba",
            "Cyprus",
            "Czechia",
            "Democratic Republic of the Congo",
            "Denmark",
            "Djibouti",
            "Dominica",
            "Dominican Republic",
            "Ecuador",
            "Egypt",
            "El Salvador",
            "Equatorial Guinea",
            "Eritrea",
            "Estonia",
            "Eswatini",
            "Ethiopia",
            "Fiji",
            "Finland",
            "France",
            "Gabon",
            "Gambia",
            "Georgia",
            "Germany",
            "Ghana",
            "Greece",
            "Grenada",
            "Guatemala",
            "Guinea",
            "Guinea-Bissau",
            "Guyana",
            "Haiti",
            "Honduras",
            "Hong Kong",
            "Hungary",
            "Iceland",
            "India",
            "Indonesia",
            "Iran",
            "Iraq",
            "Ireland",
            "Israel",
            "Italy",
            "Ivory Coast",
            "Jamaica",
            "Japan",
            "Jordan",
            "Kazakhstan",
            "Kenya",
            "Kiribati",
            "Kuwait",
            "Kyrgyzstan",
            "Laos",
            "Latvia",
            "Lebanon",
            "Lesotho",
            "Liberia",
            "Libya",
            "Liechtenstein",
            "Lithuania",
            "Luxembourg",
            "Macau",
            "Madagascar",
            "Malawi",
            "Malaysia",
            "Maldives",
            "Mali",
            "Malta",
            "Marshall Islands",
            "Mauritania",
            "Mauritius",
            "Mexico",
            "Micronesia",
            "Moldova",
            "Monaco",
            "Mongolia",
            "Montenegro",
            "Morocco",
            "Mozambique",
            "Myanmar (Burma)",
            "Namibia",
            "Nauru",
            "Nepal",
            "Netherlands",
            "New Zealand",
            "Nicaragua",
            "Niger",
            "Nigeria",
            "North Korea",
            "North Macedonia",
            "Norway",
            "Oman",
            "Pakistan",
            "Palau",
            "Palestine State",
            "Panama",
            "Papua New Guinea",
            "Paraguay",
            "Peru",
            "Philippines",
            "Poland",
            "Portugal",
            "Qatar",
            "Romania",
            "Russia",
            "Rwanda",
            "Saint Kitts and Nevis",
            "Saint Lucia",
            "Saint Vincent and the Grenadines",
            "Samoa",
            "San Marino",
            "Sao Tome and Principe",
            "Saudi Arabia",
            "Senegal",
            "Serbia",
            "Seychelles",
            "Sierra Leone",
            "Singapore",
            "Slovakia",
            "Slovenia",
            "Solomon Islands",
            "Somalia",
            "South Africa",
            "South Korea",
            "South Sudan",
            "Spain",
            "Sri Lanka",
            "Sudan",
            "Suriname",
            "Sweden",
            "Switzerland",
            "Syria",
            "Taiwan",
            "Tajikistan",
            "Tanzania",
            "Thailand",
            "Timor-Leste",
            "Togo",
            "Tonga",
            "Trinidad and Tobago",
            "Tunisia",
            "Turkey",
            "Turkmenistan",
            "Tuvalu",
            "Uganda",
            "Ukraine",
            "United Arab Emirates",
            "United Kingdom",
            "United States",
            "Uruguay",
            "Uzbekistan",
            "Vanuatu",
            "Vatican City",
            "Venezuela",
            "Vietnam",
            "Yemen",
            "Zambia",
            "Zimbabwe"
        ];
    }
   // event manaegr
    public function wtmem_event_manaegr_country_dropdown($selected_country = '') {

        $countries = $this->wtmem_get_country_list();

        echo '<div class="select-wrap">';
        echo '<select name="wtmem_ve_country" id="wtmem_ve_country" class="js-searchBox form-control">';

        foreach ($countries as $country) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($country),
                selected($selected_country, $country, false),
                esc_html($country)
            );
        }

        echo '</select>';
        echo '</div>';
    }


    // organizer
    public function wtmem_orga_render_country_dropdown($selected_country = '') {

        $countries = $this->wtmem_get_country_list();

        echo '<div class="select-wrap">';
        echo '<select name="wtmem_orga_country" id="wtmem_orga_country" class="js-searchBox form-control">';

        foreach ($countries as $country) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($country),
                selected($selected_country, $country, false),
                esc_html($country)
            );
        }

        echo '</select>';
        echo '</div>';
    }

    // Sponser
    public function wtmem_sponser_country_dropdown($selected_country = '') {

        $countries = $this->wtmem_get_country_list();
        echo '<div class="select-wrap">';
        echo '<select name="wtmem_spon_country" id="wtmem_spon_country" class="js-searchBox form-control">';
        foreach ($countries as $country) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($country),
                selected($selected_country, $country, false),
                esc_html($country)
            );
        }
        echo '</select>';
        echo '</div>';
        
    }
    
    
    // === Volunteer ====
    public function wtmem_volunteer_country_dropdown($selected_country = '') {

        $countries = $this->wtmem_get_country_list();

        echo '<div class="select-wrap">';
        echo '<select name="wtmem_volun_country" id="wtmem_volun_country" class="js-searchBox form-control">';

        foreach ($countries as $country) {
            printf(
                '<option value="%s" %s>%s</option>',
                esc_attr($country),
                selected($selected_country, $country, false),
                esc_html($country)
            );
        }

        echo '</select>';
        echo '</div>';
    }

}
?>