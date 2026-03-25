<?php
$API_KEY="f4a93d15173229253a4f234727b2902053f61bbd;popclock";


   /* POPCLOCK-963: Change the default date to yesterday. */
   $yesterday = date("Y-m-d", mktime(0, 0, 0, date("m") , date("d")-1,date("Y")));
   $year = substr($yesterday,0,4);
   $month = substr($yesterday,5,2); 
   $day   = substr($yesterday,8,2); 


$config = (object)array(
    
    // Labels for the Learn More and Download/Share links
    "component_dividing_header" => "Annual Population Estimates",
    "learn_more_link_label" => "Learn More",
    "share_link_label" => "Download and Share",
    "view_data_table_link_label" => "View Data Table",
    
    /*
     Footnotes accept HTML; however, labels should refrain from using block level elements
     as the label is wrapped in a header tag.
     Each component accepts a "label" and "content" key
     "component" => (object)array(
     "label" => "label text/html",
     "content" => "html content"
     ),
     removing the label key at the component level will result in the footnotes label being used
     if the content is removed, content for that component will be skipped
     */
    "embed_world_footnotes" => (object)array(
        "world_counter" => (object)array(
            "label" => "World Population",
            "content" => '<p>World Population Clock Source: U.S. Census Bureau, International Database (demographic data) and USA Trade Online (trade data).</p>
				<p>Populations shown for the Most Populous Countries and on the world map are projected to July 1, 2026.</p>
                                <p>To learn more about world population projections, go to <a href="https://www.census.gov/data/data-tools/population-clock/world-notes.html">Notes on the World Population Clock</a>.</p>
                                <p>To learn more about international trade data, go to <a href="https://www.census.gov/foreign-trade/guide/index.html">Guide to Foreign Trade Statistics</a>.</p>
				<p>Coordinated Universal Time (UTC) is the equivalent of Eastern Standard Time (EST) plus 5 hours or Eastern Daylight Saving Time (EDT) plus 4 hours.</p>
                               <p>Boundaries and place names depicted in this map reflect U.S. government policy wherever possible. For more information, please visit the <a href="https://www.nga.mil/resources/US_Board_on_Geographic_Names_.html" target="_blank">U.S. Board on Geographic Names (Foreign Names Committee)</a> and the <a href="https://geodata.state.gov/geonetwork/srv/eng/catalog.search#/home" target="_blank">U.S. Department of State (Office of the Geographer and Global Issues)</a>.
</p>
'
        ),
    ),
    
    "world_footnotes" => (object)array(
        // Label for entire footnote section
        "label" => "About the Population Clock and Population Estimates",
        
        // Map
        "map" => (object)array(
            "label" => "Source and Notes",
            // <p>Source:  U.S. Census Bureau, <a href="/population/international/data/idb/informationGateway.php">International Data Base</a></p>
            //"content" => '<p>Source: U.S. Census Bureau, <a href="/population/international/data/idb/informationGateway.php">International Data Base</a> (demographic data) and <a href="http://usatrade.census.gov/">USA Trade Online</a> (trade data).</p>
            "content" => '<p>Source: U.S. Census Bureau, <a href="https://www.census.gov/programs-surveys/international-programs/about/idb.html">International Database</a> (demographic data) and <a href="http://usatrade.census.gov/">USA Trade Online</a> (trade data).</p>
				<p>Populations shown for the Most Populous Countries and on the world map are projected to July 1, 2026.</p>

                                <p>To learn more about world population projections, go to <a href="https://www.census.gov/data/data-tools/population-clock/world-notes.html">Notes on the World Population Clock</a>.</p>
                                <p>To learn more about international trade data, go to <a href="https://www.census.gov/foreign-trade/guide/index.html">Guide to Foreign Trade Statistics</a>.</p>




				<p>All trade figures are in U.S. dollars on a nominal basis.</p>
				<p>Coordinated Universal Time (UTC) is the equivalent of Eastern Standard Time (EST) plus 5 hours or Eastern Daylight Saving Time (EDT) plus 4 hours.</p>
                               <p>Boundaries and place names depicted in this map reflect U.S. government policy wherever possible. For more information, please visit the <a href="https://www.nga.mil/resources/US_Board_on_Geographic_Names_.html" target="_blank">U.S. Board on Geographic Names (Foreign Names Committee)</a> and the <a href="https://geodata.state.gov/geonetwork/srv/eng/catalog.search#/home" target="_blank">U.S. Department of State (Office of the Geographer and Global Issues)</a>.
 </p>'
        ),
        
        
        // Country profile
        "country" => (object)array(
            "label" => "Source and Notes",
            // <p>Source: U.S. Census Bureau, <a href="/population/international/data/idb/informationGateway.php">International Data Base</a> (demographic data) and <a href="http://usatrade.census.gov">USA Trade Online</a> (trade data).</p>
            //"content" => '<p>Source: U.S. Census Bureau, <a href="/population/international/data/idb/informationGateway.php">International Data Base</a> (demographic data) and <a href="http://usatrade.census.gov">USA Trade Online</a> (trade data); Central Intelligence Agency, <a href="https://www.cia.gov/library/publications/the-world-factbook/">The World Fact Book</a> (country reference maps).</p>
            "content" => '<p>Source: U.S. Census Bureau, <a href="https://www.census.gov/programs-surveys/international-programs/about/idb.html">International Database</a> (demographic data) and <a href="http://usatrade.census.gov">USA Trade Online</a> (trade data); Central Intelligence Agency, The World Fact Book (country reference maps).</p>
				<p>This application presents data for 228 countries and areas of the world with a 2026 population of 5,000 or more. For eleven of those countries and areas, only demographic data are presented. </p>
				<p>"Data not available" indicates that data are not available from this application.  In such instances, data may be available elsewhere at the census.gov website.</p>
				<p>Trade figures presented for France do not include data for overseas departments French Guiana, Guadeloupe, Martinique, Mayotte, and Reunion but do include data for dependencies Saint Barthelemy and Saint Martin.  Trade figures for the United Kingdom include data for crown dependencies Guernsey, Isle of Man, and Jersey.</p>
                                <p>To learn more about world population projections, go to <a href="https://www.census.gov/data/data-tools/population-clock/world-notes.html">Notes on the World Population Clock</a>.</p>
                                <p>To learn more about international trade data, go to <a href="https://www.census.gov/foreign-trade/guide/index.html">Guide to Foreign Trade Statistics</a>.</p>
				<p>All trade figures are in U.S. dollars on a nominal basis.</p>
                               <p>Boundaries and place names depicted in this map reflect U.S. government policy wherever possible. For more information, please visit the <a href="https://www.nga.mil/resources/US_Board_on_Geographic_Names_.html" target="_blank">U.S. Board on Geographic Names (Foreign Names Committee)</a> and the <a href="https://geodata.state.gov/geonetwork/srv/eng/catalog.search#/home" target="_blank">U.S. Department of State (Office of the Geographer and Global Issues)</a>.
 </p>  '
        ),
        
    ),
    "footnotes" => (object)array(
        
        // Label for entire footnote section
        "label" => "About the Population Clock and Population Estimates",
        
        // Top Clocks
        "counter" => (object)array(
            "label" => "U.S. Population",
            "content" => '
                     <p>The U.S. population clock is based on a series of short-term projections for the resident population of the United States. This includes people whose usual residence is in the 50 states and the District of Columbia. These projections do not include members of the Armed Forces overseas, their dependents, or other U.S. citizens residing outside the United States.</p>
                     <p>The projections are based on a monthly series of population estimates starting with the April 1, 2020 resident population from the 2020 Census.</p>
		<p>At the end of each year, a revised series of population estimates from the census date forward is used to update the short-term projections for the population clock. Once the updated series of monthly projections is completed, the daily population clock values are derived by interpolation. Within each calendar month, the daily numerical population change is assumed to be constant, subject to negligible differences caused by rounding.</p>
				<p>Population estimates produced by the U.S. Census Bureau for the United States, states, metropolitan and micropolitan statistical areas, counties, cities, towns, as well as for Puerto Rico and its municipios can be found on the <a href="//www.census.gov/programs-surveys/popest.html"> Population Estimates</a> web page. Projections of the future population for the United States can be found on the <a href="//www.census.gov/programs-surveys/popproj.html">Population Projections</a> web page.</p>
<p>Cities featured on the Most Populous and Highest Density lists feature populations of 5,000 or more on July 1, 2024.</p>'
            
        ),
        
        // Lookup by Date
        "pop_on_date" => (object)array(
            "label" => "",
            "content" => ""
        ),
        
        // Growth Stacked Line Chart
        "growth" => (object)array(
            "label" => "",
            "content" => ""
        ),
        
        // Age/Sex Pyramid Graph
        "pyramid" => (object)array(
            "label" => "",
            "content" => ""
        ),
        
        // Most Populous Tables
        "populous" => (object)array(
            "label" => "",
            "content" => ""
        ),
        
        // Most Dense Tables
        "density" => (object)array(
            "label" => "",
            "content" => ""
        ),
    ),
    
    // Sections
    "components" => (object)array(
        
        "counter" => (object)array(
            "label" => "Population Counters",
        ),
        
        // US Clock
        "us" => (object)array(
            "label" => "U.S. Population",
            "url" => _APPROOT_ . '?intcmp=w_200x402',
            "interval" => 15,
            "birth_rate_label" => "One birth every <strong>%@ seconds</strong>",
            "death_rate_label" => "One death every <strong>%@ seconds</strong>",
            "immigrant_rate_label" => "One international migrant (net) every <strong>%@ seconds</strong>",
            "net_gain_label" => "Net gain of one person every <strong>%@ seconds</strong>",
            "learn_more_link_title" => "Placeholder title for component link 1", // for 508 compliance this string is placed in the link itself and are note visible
            "share_link_title" => "Placeholder title for component link 1" // for 508 compliance this string is placed in the link itself and are note visible
            
        ),
        "us_rates" => (object)array(
            // this is not an individually sharable compoent
            // therefore, does not have a learn more or share link
            "label" => "Components of Population Change",
            "table_summary" => "Placeholder for population rates table summary 1", // for 508 compliance this is place in the table's summary attribute
            "table_header_labels" => array(
                "Rates",
                "Visualization"
            )
        ),
        
        // World Clock
        "world" => (object)array(
            // this is not an individually sharable compoent
            // therefore, does not have a learn more or share link
            "label" => "World Population",
            "url" => _APPROOT_ . "world?intcmp=w_200x402",
            "interval" => 0.1
        ),
        
        "world_rates" => (object)array(
            // this is not an individually sharable compoent
            // therefore, does not have a learn more or share link
            "label" => '<a href="./world" target="_parent" title="International Programs - Country Rank">TOP 10 MOST POPULOUS COUNTRIES (July 1, 2026) </a>',
            
            "tables" => array(
                (object)array(
                    "title" => '<a href="./world" title="International Programs - Country Rank">TOP 10 MOST POPULOUS COUNTRIES (July 1, 2026) </a>',
                    "columns" => array(
                        'Rank. Country',
                        'Population',
                        'Rank. Country',
                        'Population'
                    ),
                    "rows" => array(
                        'India' => 1429700205,
                        'China' => 1405918803,
                        'United States' => 342620143,
                        'Indonesia' => 285562809,
                        'Pakistan' =>  	261714024,
                        'Nigeria' => 250228859,
                        'Brazil'  => 222624000,
                        'Bangladesh' => 175927589,
                        'Russia' => 139450252,
                        'Mexico' => 132807523
                    ),
                    "vintage" => "2012",
                    "table_summary" => "Placeholder for population rates table summary 5" // for 508 compliance this is place in the table's summary attribute
                ),
                (object)array(
                    "title" => '<a href="./population/international/data/countryrank/rank.php" title="International Programs - Country Rank">Top 10 Fastest Growing Countries</a>',
                    "columns" => array(
                        'Rank',
                        'Country'
                    ),
                    "rows" => array(
                        'India' => 1419316933,
                        'China' => 1407181209,
                        'United States' => 342034432,
                        'Indonesia' => 283587097,
                        'Pakistan' =>  	257047044,
                        'Nigeria' => 244344065,
                        'Brazil'  => 221359387,
                        'Bangladesh' => 174370536,
                        'Russia' => 140134279,
                        'Mexico' => 131741347
                    ),
                    "vintage" => "2012",
                    "table_summary" => "Placeholder for population rates table summary 6" // for 508 compliance this is place in the table's summary attribute
                )
            )
        ),
        
        // Lookup by Date
        /* (POPCLOCK-936) The default date is being changed from 7/4 to 
         * yesteday.
         */
        "pop_on_date" => (object)array(
            "label" => "The United States population on %@ %@: %@",
            "default_year" => $year, 
            "default_month" => $month, 
            "default_day" => $day,
            "min_year" => 2020,
            "min_month" => 4,
            "min_day" => 1,
            "learn_more_link_title" => "Placeholder title for component link 2", // for 508 compliance this string is placed in the link itself and are note visible
            "share_link_title" => "Placeholder title for component link 2" // for 508 compliance this string is placed in the link itself and are note visible
        ),
        
        // Growth Stacked Line Chart
        //United States Population Growth by Region
        "growth" => (object)array(
            "label" => "United States Population Growth by Region",
            "y_axis_label" => "Population (in millions)",
            "min_year" => 2020,
            "max_year" => 2025,
            "learn_more_link_title" => "Placeholder title for component link 3", // for 508 compliance this string is placed in the link itself and are note visible
            "share_link_title" => "Placeholder title for component link 3", // for 508 compliance this string is placed in the link itself and are note visible
            "view_data_table_link_title" => "Placeholder title for component link3",
            "table_summary" => "Placeholder for population rates table summary 3", // for 508 compliance this is place in the table's summary attribute (the table for each year has the same summary)
            "table_header_labels" => array(
                "Region",
                "Population",
                "Visualization",
                "Percentage"
            )
        ),
        
        
        // Age/Sex Pyramid Graph
        "pyramid" => (object)array(
            "label" => "United States Population by Age and Sex",
            "x_axis_label_middle" => "% of Population",
            "x_axis_label_modifier" => "%",
            //"min_year" => 2000,
            "min_year" => 2020,
            "max_year" => 2024,
            "max_total_percentage" => 1,
            "table_summary" => "Placeholder for population rates table summary 4", // for 508 compliance this is place in the table's summary attribute (the table for each year has the same summary)
            "learn_more_link_title" => "Placeholder title for component link 4", // for 508 compliance this string is placed in the link itself and are note visible
            "share_link_title" => "Placeholder title for component link 4", // for 508 compliance this string is placed in the link itself and are note visible
            "view_data_table_link_title" => "Placeholder title for component link4"
        ),
        
        // Most Populous Tables
        "populous" => (object)array(
            "label" => "Most Populous",
            "learn_more_link_title" => "Placeholder title for component link 5", // for 508 compliance this string is placed in the link itself and are note visible
            "share_link_title" => "Placeholder title for component link 5", // for 508 compliance this string is placed in the link itself and are note visible
            "tables" => array(
                (object)array(
                    "title" => 'States',
                    "columns" => array(
                        'State',
                        'Population',
                        'Pop. per sq. mi.'
                    ),
                    "vintage" => "2025",
                    "table_summary" => "Placeholder for population rates table summary 7" // for 508 compliance this is place in the table's summary attribute
                ),
                (object)array(
                    "title" => 'Counties',
                    "columns" => array(
                        'County',
                        'Population',
                        'Pop. per sq. mi.'
                    ),
                    "vintage" => "2024",
                    "table_summary" => "Placeholder for population rates table summary 6" // for 508 compliance this is place in the table's summary attribute
                ),
                (object)array(
                    "title" => 'Cities',
                    "columns" => array(
                        'City, ST',
                        'Population',
                        'Pop. per sq. mi.'
                    ),
                    "vintage" => "2024",
                    "table_summary" => "Placeholder for population rates table summary 5" // for 508 compliance this is place in the table's summary attribute
                )
            )
        ),
        
        // Most Dense Tables
        "density" => (object)array(
            "label" => "Highest Density",
            "learn_more_link_title" => "Placeholder title for component link 6", // for 508 compliance this string is placed in the link itself and are note visible
            "share_link_title" => "Placeholder title for component link 6", // for 508 compliance this string is placed in the link itself and are note visible
            "tables" => array(
                (object)array(
                    "title" => 'States',
                    "columns" => array(
                        'State',
                        'Population',
                        'Pop. per sq. mi.'
                    ),
                    "vintage" => "2025",
                    "table_summary" => "Placeholder for population rates table summary 10" // for 508 compliance this is place in the table's summary attribute
                ),
                (object)array(
                    "title" => 'Counties',
                    "columns" => array(
                        'County',
                        'Population',
                        'Pop. per sq. mi.'
                    ),
                    "vintage" => "2024",
                    "table_summary" => "Placeholder for population rates table summary 9" // for 508 compliance this is place in the table's summary attribute
                    
                ),
                (object)array(
                    "title" => 'Cities',
                    "columns" => array(
                        'City, ST',
                        'Population',
                        'Pop. per sq. mi.'
                    ),
                    "vintage" => "2024",
                    "table_summary" => "Placeholder for population rates table summary 8" // for 508 compliance this is place in the table's summary attribute
                )
                
            )
        )
    ),
    
    // API Links
    "api" => (object)array(
        "cache" => false,
        "methods" => (object)array(
            
            // US Clock
            "us" => (object)array(
                "url" => _APPROOT_ . "data/population.php/us"
            ),
            
            // World Clock
            "world" => (object)array(
                "url" => _APPROOT_ . "data/population.php/world"
            ),
            
            // Lookup by Date
            "pop_on_date" => (object)array(
                "url" => _APPROOT_ . "data/population.php/us"
            ),
            
            // Growth Stacked Line Chart
            "region" => (object)array(
                "url" => _APPROOT_ . "data/population.php/region",
                "data" => (object)array(
                    "regions" => "west,midwest,northeast,south"
                )
            ),
            
            // Age/Sex Pyramid Graph
            "demographic" => (object)array(
                "url" => _APPROOT_ . "data/population.php/demographic"
            ),
            
            // Most Populous Tables
            "populous" => (object)array(
                "url" => _APPROOT_ . "data/population.php/rank",
                "data" => (object)array(
                    "type" => "states,counties,cities",
                    "sort" => "population:DESC",
                    "limit" => 10
                )
            ),
            
            // Most Dense Tables
            "density" => (object)array(
                "url" => _APPROOT_ . "data/population.php/rank",
                "data" => (object)array(
                    "type" => "states,counties,cities",
                    "sort" => "density:DESC",
                    "limit" => 10
                )
            )
            
        )
    ),
    
    // Share Language
    "share" => (object)array(
        "components" => (object)array(
            // You may use template replacement tokens in this text:
            // %url% = The URL key (below)
            // %image% = The image key (below)
            
            // Top Clocks
            "counter" => (object)array(
                "pinterest" => "Every minute the US #population is changing, see population changes @uscensusbureau Population Clock",
                "facebook" => "Every minute the U.S. population is changing, see how quickly it changes with the Census' Population Clock.",
                "twitter" => "Every minute the US #population is changing, see population changes @uscensusbureau Population Clock",
                "email" => "Every minute the U.S. population is changing, see how quickly it changes with the Census' Population Clock. %url%",
            ),
            
            // Lookup by Date
            "pop_on_date" =>(object)array(
                // Template replacement tokens include %date% and %population%
                "pinterest" => "Did u know the US #population on %date% was %population%? Discover more with @uscensusbureau Population Clock",
                "facebook" => "Did you know the U.S. population on %date% was %population%? Discover how it has changed since with the new Census' Population Clock.",
                "twitter" => "Did u know the US #population on %date% was %population%? Discover more with @uscensusbureau Population Clock",
                "email" => "Did you know the U.S. population on %date% was %population%? Discover how it has changed since with the new Census' Population Clock. %url%",
            ),
            
            // Growth Stacked Line Chart
            "growth" => (object)array(
                "pinterest" => "How is the US #population changing, region by region? Find out with #Census' Population Clock. @uscensusbureau",
                "facebook" => "How is the U.S. population changing, region by region? Find out with the new Census' Population Clock.",
                "twitter" => "How is the US #population changing, region by region? Find out with #Census' Population Clock. @uscensusbureau %image%",
                "email" => "How is the U.S. population changing, region by region? Find out with the new Census' Population Clock. %url%",
            ),
            
            // Age/Sex Pyramid Graph
            "pyramid" => (object)array(
                "pinterest" => "What % of US women are #age 35? Men who are 58? Find out with @uscensusbureau #Population Clock",
                "facebook" => "What percentage of U.S. women are age 35? Men who are 58? Find out with the new Census' Population Clock.",
                "twitter" => "What % of US women are #age 35? Men who are 58? Find out with @uscensusbureau #Population Clock %image%",
                "email" => "What percentage of U.S. women are age 35? Men who are 58? Find out with the new Census' Population Clock. %url%",
            ),
            
            // Most Populous Tables
            "populous" => (object)array(
                "pinterest" => "What are the most populous US states, counties and cities? Find out with @uscensusbureau #Population Clock.",
                "facebook" => "What are the most populous U.S. states, counties and cities? Find out with the new Census' Population Clock.",
                "twitter" => "What are the most populous US states, counties and cities? Find out with @uscensusbureau #Population Clock.",
                "email" => "What are the most populous U.S. states, counties and cities? Find out with the new Census' Population Clock. %url%",
            ),
            
            // Most Dense Tables
            "density" => (object)array(
                "pinterest" => "Which US states and counties have the highest #population densities? Find out with @uscensusbureau Population Clock.",
                "facebook" => "Which U.S. states and counties have the highest population densities? Find out with the new Census Population Clock.",
                "twitter" => "Which US states and counties have the highest #population densities? Find out with @uscensusbureau Population Clock.",
                "email" => "Which U.S. states and counties have the highest population densities? Find out with the new Census Population Clock. %url%",
            ),
        ),
        
        // Pinterest Custom Settings
        "pinterest" => (object)array(
            // No custom settings
        ),
        
        // Facebook Custom Settings
        "facebook" => (object)array(
            "title" => "Census Population Clock",
            "site_name" => "Census.gov",
            "type" => "government",
            "tags" => (object)array(
                "admins" => "100003365860983", // FB ID of main admin currently set to a HFC Employee; this should definitely get replaced
            ),
        ),
        
        // Twitter Custom Settings
        "twitter" => (object)array(
            // No custom settings
        ),
        
        // Email Custom Settings
        "email" => (object)array(
            "to" => "",
            "subject" => "Check out the Census Population Clock",
        ),
        
        // URL token
        "url" => "http://go.usa.gov/2Y45", // _URL_ . "/"
        
        // Image token - used for counter, pop_on_date, populous and density. (custom images are generated for growth and pyramid)
        "image" => _URL_ . '/images/census-logo-whiteBG.png',
    )
);
?>
