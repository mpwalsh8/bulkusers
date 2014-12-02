<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
/**
 * Plugin Name: BulkUsers
 * Plugin URI: https://github.com/mpwalsh8/bulkusers
 * Description: WordPress plugin to populate the WordPress database with demo users.
 * Version: 0.1
 * Author: Mike Walsh
 * Author URI: http://www.michaelwalsh.org/
 * License: GPL
 * 
 *
 * (c) 2014 by Mike Walsh
 *
 * @author Mike Walsh <mpwalsh8@gmail.com>
 * @package BulkUsers
 * @subpackage none
 * @version 0.1
 * @lastmodified 2014-12-02
 * @lastmodifiedby mpwalsh8
 *
 */

//  Set up some parameters to control the generation of user accounts
//
//  Notes:
//
//  1.  Edit these settings prior to plugin activation to change behavior.
//  2.  When BULKUSERS_USE_ADMIN_EMAIL_PLUS_SUFFIX is set to true the email
//      address of the generated user will take on the format of the admin
//      email address plus a suffix (username) which will look like this:
//
//      Admin email:  mpwalsh8@gmail.com
//      Generated username:  nbaker
//      Generated user email address:  mpwalsh8+nbaker@gmail.com
//
//      This format of email will satisfy WordPress' requirement to have a
//      unique email address for each user AND allow delivery of the actual
//      email, if desired, for testing purposes.  Be careful with this feature
//      as sending 2000 emails to yourself with slightly different email addresses
//      could very easily end up being classified as spam.
//
//  3.  If your domain supports a "catch all" email address, using the email domain
//      should result in the catch all address receiving multiple copies of the same
//      email.  Again, spam classification is high so use at your own risk. 
//

define('BULKUSERS_DEMO_MIN_USERS', 25) ;
define('BULKUSERS_DEMO_MAX_USERS', 500) ;
define('BULKUSERS_EMAIL_DOMAIN', 'bulkusers.example.com') ;
define('BULKUSERS_USE_ADMIN_EMAIL_PLUS_SUFFIX', false) ;

//  Need the WordPress user acount
//  creation and registration stuff
require_once(ABSPATH . 'wp-load.php');

/*
 * bulkusers_install()
 *
 * Install the Bulk Users Demo plugin.  Create a bunch of WordPress
 * users and insert them into the database.
 *
 */
function bulkusers_install()
{
    $bulkusers_count = 0 ;
    $bulkusers_status = array() ;

    // male first names - generated from:
    // http://www.ruf.rice.edu/~pound/english-m

    $malenames = array('Aaron', 'Adam', 'Adrian', 'Alan', 'Alejandro', 'Alex',
        'Allen', 'Andrew', 'Andy', 'Anthony', 'Art', 'Arthur', 'Barry',
        'Bart', 'Ben', 'Benjamin', 'Bill', 'Bobby', 'Brad', 'Bradley',
        'Brendan', 'Brett', 'Brian', 'Bruce', 'Bryan', 'Carlos', 'Chad',
        'Charles', 'Chris', 'Christopher', 'Chuck', 'Clay', 'Corey', 'Craig',
        'Dan', 'Daniel', 'Darren', 'Dave', 'David', 'Dean', 'Dennis', 'Denny',
        'Derek', 'Don', 'Doug', 'Duane', 'Edward', 'Eric', 'Eugene', 'Evan',
        'Frank', 'Fred', 'Gary', 'Gene', 'George', 'Gordon', 'Greg', 'Harry',
        'Henry', 'Hunter', 'Ivan', 'Jack', 'James', 'Jamie', 'Jason', 'Jay',
        'Jeff', 'Jeffrey', 'Jeremy', 'Jim', 'Joe', 'Joel', 'John', 'Jonathan',
        'Joseph', 'Justin', 'Keith', 'Ken', 'Kevin', 'Larry', 'Logan', 'Marc',
        'Mark', 'Matt', 'Matthew', 'Michael', 'Mike', 'Nat', 'Nathan',
        'Patrick', 'Paul', 'Perry', 'Peter', 'Philip', 'Phillip', 'Randy',
        'Raymond', 'Ricardo', 'Richard', 'Rick', 'Rob', 'Robert', 'Rod',
        'Roger', 'Ross', 'Ruben', 'Russell', 'Ryan', 'Sam', 'Scot', 'Scott',
        'Sean', 'Shaun', 'Stephen', 'Steve', 'Steven', 'Stewart', 'Stuart',
        'Ted', 'Thomas', 'Tim', 'Toby', 'Todd', 'Tom', 'Troy', 'Victor',
        'Wade', 'Walter', 'Wayne', 'William') ;

    // female first names - generated from:
    // http://www.ruf.rice.edu/~pound/english-f

    $femalenames = array('Aimee', 'Aleksandra', 'Alice', 'Alicia', 'Allison',
        'Alyssa', 'Amy', 'Andrea', 'Angel', 'Angela', 'Ann', 'Anna', 'Anne',
        'Anne', 'Marie', 'Annie', 'Ashley', 'Barbara', 'Beatrice', 'Beth',
        'Betty', 'Brenda', 'Brooke', 'Candace', 'Cara', 'Caren', 'Carol',
        'Caroline', 'Carolyn', 'Carrie', 'Cassandra', 'Catherine',
        'Charlotte', 'Chrissy', 'Christen', 'Christina', 'Christine',
        'Christy', 'Claire', 'Claudia', 'Courtney', 'Crystal', 'Cynthia',
        'Dana', 'Danielle', 'Deanne', 'Deborah', 'Deirdre', 'Denise',
        'Diane', 'Dianne', 'Dorothy', 'Eileen', 'Elena', 'Elizabeth', 'Emily',
        'Erica', 'Erin', 'Frances', 'Gina', 'Giulietta', 'Heather', 'Helen',
        'Jane', 'Janet', 'Janice', 'Jenna', 'Jennifer', 'Jessica', 'Joanna',
        'Joyce', 'Julia', 'Juliana', 'Julie', 'Justine', 'Kara', 'Karen',
        'Katharine', 'Katherine', 'Kathleen', 'Kathryn', 'Katrina', 'Kelly',
        'Kerry', 'Kim', 'Kimberly', 'Kristen', 'Kristina', 'Kristine', 'Laura',
        'Laurel', 'Lauren', 'Laurie', 'Leah', 'Linda', 'Lisa', 'Lori', 'Marcia',
        'Margaret', 'Maria', 'Marina', 'Marisa', 'Martha', 'Mary', 'Mary Ann',
        'Maya', 'Melanie', 'Melissa', 'Michelle', 'Monica', 'Nancy', 'Natalie',
        'Nicole', 'Nina', 'Pamela', 'Patricia', 'Rachel', 'Rebecca', 'Renee',
        'Sandra', 'Sara', 'Sharon', 'Sheri', 'Shirley', 'Sonia', 'Stefanie',
        'Stephanie', 'Susan', 'Suzanne', 'Sylvia', 'Tamara', 'Tara', 'Tatiana',
        'Terri', 'Theresa', 'Tiffany', 'Tracy', 'Valerie', 'Veronica', 'Vicky',
        'Vivian', 'Wendy') ;

    // surnames - generated from:
    // http://www.ruf.rice.edu/~pound/english-s

    $surnames = array('Adams', 'Adamson', 'Adler', 'Akers', 'Akin', 'Aleman',
        'Alexander', 'Allen', 'Allison', 'Allwood', 'Anderson', 'Andreou',
        'Anthony', 'Appelbaum', 'Applegate', 'Arbore', 'Arenson', 'Armold',
        'Arntzen', 'Askew', 'Athanas', 'Atkinson', 'Ausman', 'Austin',
        'Averitt', 'Avila-Sakar', 'Badders', 'Baer', 'Baggerly', 'Bailliet',
        'Baird', 'Baker', 'Ball', 'Ballentine', 'Ballew', 'Banks',
        'Baptist-Nguyen', 'Barbee', 'Barber', 'Barchas', 'Barcio', 'Bardsley',
        'Barkauskas', 'Barnes', 'Barnett', 'Barnwell', 'Barrera', 'Barreto',
        'Barroso', 'Barrow', 'Bart', 'Barton', 'Bass', 'Bates', 'Bavinger',
        'Baxter', 'Bazaldua', 'Becker', 'Beeghly', 'Belforte', 'Bellamy',
        'Bellavance', 'Beltran', 'Belusar', 'Bennett', 'Benoit', 'Bensley',
        'Berger', 'Berggren', 'Bergman', 'Berry', 'Bertelson', 'Bess',
        'Beusse', 'Bickford', 'Bierner', 'Bird', 'Birdwell', 'Bixby',
        'Blackmon', 'Blackwell', 'Blair', 'Blankinship', 'Blanton', 'Block',
        'Blomkalns', 'Bloomfield', 'Blume', 'Boeckenhauer', 'Bolding', 'Bolt',
        'Bolton', 'Book', 'Boucher', 'Boudreau', 'Bowman', 'Boyd', 'Boyes',
        'Boyles', 'Braby', 'Braden', 'Bradley', 'Brady', 'Bragg', 'Brandow',
        'Brantley', 'Brauner', 'Braunhardt', 'Bray', 'Bredenberg', 'Bremer',
        'Breyer', 'Bricout', 'Briggs', 'Brittain', 'Brockman', 'Brockmoller',
        'Broman', 'Brooks', 'Brown', 'Brubaker', 'Bruce', 'Brumfield',
        'Brumley', 'Bruning', 'Buck', 'Budd', 'Buhler', 'Buhr', 'Burleson',
        'Burns', 'Burton', 'Bush', 'Butterfield', 'Byers', 'Byon', 'Byrd',
        'Bzostek', 'Cabrera', 'Caesar', 'Caffey', 'Caffrey', 'Calhoun',
        'Call', 'Callahan', 'Campbell', 'Cano', 'Capri', 'Carey', 'Carlisle',
        'Carlson', 'Carmichael', 'Carnes', 'Carr', 'Carreira', 'Carroll',
        'Carson', 'Carswell', 'Carter', 'Cartwright', 'Cason', 'Cates',
        'Catlett', 'Caudle', 'Cavallaro', 'Cave', 'Cazamias', 'Chabot',
        'Chance', 'Chapman', 'Characklis', 'Cheatham', 'Chen', 'Chern',
        'Cheville', 'Chong', 'Christensen', 'Church', 'Claibourn', 'Clark',
        'Clasen', 'Claude', 'Close', 'Coakley', 'Coffey', 'Cohen', 'Cole', 
        'Collier', 'Conant', 'Connell', 'Conte', 'Conway', 'Cooley', 'Cooper',
        'Copeland', 'Coram', 'Corbett', 'Cort', 'Cortes', 'Cousins', 'Cowsar',
        'Cox', 'Coyne', 'Crain', 'Crankshaw', 'Craven', 'Crawford', 'Cressman',
        'Crestani', 'Crier', 'Crocker', 'Cromwell', 'Crouse', 'Crowder',
        'Crowe', 'Culpepper', 'Cummings', 'Cunningham', 'Currie', 'Cusey',
        'Cutcher', 'Cyprus', 'D\'Ascenzo', 'Dabak', 'Dakoulas', 'Daly',
        'Dana', 'Danburg', 'Danenhauer', 'Darley', 'Darrouzet', 'Dartt',
        'Daugherty', 'Davila', 'Davis', 'Dawkins', 'Day', 'DeHart', 'DeMoss',
        'DeMuth', 'DeVincentis', 'Deaton', 'Dees', 'Degenhardt', 'Deggeller',
        'Deigaard', 'Delabroy', 'Delaney', 'Demir', 'Denison', 'Denney',
        'Derr', 'Deuel', 'Devitt', 'Diamond', 'Dickinson', 'Dietrich',
        'Dilbeck', 'Dobson', 'Dodds', 'Dodson', 'Doherty', 'Dooley',
        'Dorsey', 'Dortch', 'Doughty', 'Dove', 'Dowd', 'Dowling', 'Drescher',
        'Drucker', 'Dryer', 'Dryver', 'Duckworth', 'Dunbar', 'Dunham',
        'Dunn', 'Duston', 'Dettweiler', 'Dyson', 'Eason', 'Eaton', 'Ebert',
        'Eckhoff', 'Edelman', 'Edmonds', 'Eichhorn', 'Eisbach', 'Elders',
        'Elias', 'Elijah', 'Elizabeth', 'Elliott', 'Elliston', 'Elms',
        'Emerson', 'Engelberg', 'Engle', 'Eplett', 'Epp', 'Erickson',
        'Estades', 'Etezadi', 'Evans', 'Ewing', 'Fair', 'Farfan', 'Fargason',
        'Farhat', 'Farry', 'Fawcett', 'Faye', 'Federle', 'Felcher', 'Feldman',
        'Ferguson', 'Fergusson', 'Fernandez', 'Ferrer', 'Fine', 'Fineman',
        'Fisher', 'Flanagan', 'Flathmann', 'Fleming', 'Fletcher', 'Folk',
        'Fortune', 'Fossati', 'Foster', 'Foulston', 'Fowler', 'Fox',
        'Francis', 'Frantom', 'Franz', 'Frazer', 'Fredericks', 'Frey',
        'Freymann', 'Fuentes', 'Fuller', 'Fundling', 'Furlong', 'Gainer',
        'Galang', 'Galeazzi', 'Gamse', 'Gannaway', 'Garcia', 'Gardner',
        'Garneau', 'Gartler', 'Garverick', 'Garza', 'Gatt', 'Gattis',
        'Gayman', 'Geiger', 'Gelder', 'George', 'Gerbino', 'Gerbode',
        'Gibson', 'Gifford', 'Gillespie', 'Gillingham', 'Gilpin', 'Gilyot',
        'Girgis', 'Gjertsen', 'Glantz', 'Glaze', 'Glenn', 'Glotzbach',
        'Gobble', 'Gockenbach', 'Goff', 'Goffin', 'Golden', 'Goldwyn',
        'Gomez', 'Gonzalez', 'Good', 'Graham', 'Gramm', 'Granlund', 'Grant',
        'Gray', 'Grayson', 'Greene', 'Greenslade', 'Greenwood', 'Greer',
        'Griffin', 'Grinstein', 'Grisham', 'Gross', 'Grove', 'Guthrie',
        'Guyton', 'Haas', 'Hackney', 'Haddock', 'Hagelstein', 'Hagen',
        'Haggard', 'Haines', 'Hale', 'Haley', 'Hall', 'Halladay', 'Hamill',
        'Hamilton', 'Hammer', 'Hancock', 'Hane', 'Hansen', 'Harding',
        'Harless', 'Harms', 'Harper', 'Harrigan', 'Harris', 'Harrison',
        'Hart', 'Harton', 'Hartz', 'Harvey', 'Hastings', 'Hauenstein',
        'Haushalter', 'Haven', 'Hawes', 'Hawkins', 'Hawley', 'Haygood',
        'Haylock', 'Hazard', 'Heath', 'Heidel', 'Heins', 'Hellums',
        'Hendricks', 'Henry', 'Henson', 'Herbert', 'Herman', 'Hernandez',
        'Herrera', 'Hertzmann', 'Hewitt', 'Hightower', 'Hildebrand', 'Hill',
        'Hindman', 'Hirasaki', 'Hirsh', 'Hochman', 'Hocker', 'Hoffman',
        'Hoffmann', 'Holder', 'Holland', 'Holloman', 'Holstein', 'Holt',
        'Holzer', 'Honeyman', 'Hood', 'Hooks', 'Hopper', 'Horne', 'House',
        'Houston', 'Howard', 'Howell', 'Howley', 'Huang', 'Hudgings',
        'Huffman', 'Hughes', 'Humphrey', 'Hunt', 'Hunter', 'Hurley',
        'Huston', 'Hutchinson', 'Hyatt', 'Irving', 'Jacobs', 'Jaramillo',
        'Jaranson', 'Jarboe', 'Jarrell', 'Jenkins', 'Johnson', 'Johnston',
        'Jones', 'Joy', 'Juette', 'Julicher', 'Jumper', 'Kabir', 'Kamberova',
        'Kamen', 'Kamine', 'Kampe', 'Kane', 'Kang', 'Kapetanovic', 'Kargatis',
        'Karlin', 'Karlsson', 'Kasbekar', 'Kasper', 'Kastensmidt', 'Katz',
        'Kauffman', 'Kavanagh', 'Kaydos', 'Kearsley', 'Keleher', 'Kelly',
        'Kelty', 'Kendrick', 'Key', 'Kicinski', 'Kiefer', 'Kielt', 'Kim',
        'Kimmel', 'Kincaid', 'King', 'Kinney', 'Kipp', 'Kirby', 'Kirk',
        'Kirkland', 'Kirkpatrick', 'Klamczynski', 'Klein', 'Kopnicky',
        'Kotsonis', 'Koutras', 'Kramer', 'Kremer', 'Krohn', 'Kuhlken',
        'Kunitz', 'LaLonde', 'LaValle', 'LaWare', 'Lacy', 'Lam', 'Lamb',
        'Lampkin', 'Lane', 'Langston', 'Lanier', 'Larsen', 'Lassiter',
        'Latchford', 'Lawera', 'LeBlanc', 'LeGrand', 'Leatherbury', 'Lebron',
        'Ledman', 'Lee', 'Leinenbach', 'Leslie', 'Levy', 'Lewis',
        'Lichtenstein', 'Lisowski', 'Liston', 'Litvak', 'Llano-Restrepo',
        'Lloyd', 'Lock', 'Lodge', 'Logan', 'Lomonaco', 'Long', 'Lopez',
        'Lopez-Bassols', 'Loren', 'Loughridge', 'Love', 'Ludtke', 'Luers',
        'Lukes', 'Luxemburg', 'MacAllister', 'MacLeod', 'Mackey', 'Maddox',
        'Magee', 'Mallinson', 'Mann', 'Manning', 'Manthos', 'Marie', 'Marrow',
        'Marshall', 'Martin', 'Martinez', 'Martisek', 'Massey', 'Mathis',
        'Matt', 'Maxwell', 'Mayer', 'Mazurek', 'McAdams', 'McAfee',
        'McAlexander', 'McBride', 'McCarthy', 'McClure', 'McCord', 'McCoy',
        'McCrary', 'McCrossin', 'McDonald', 'McElfresh', 'McFarland',
        'McGarr', 'McGhee', 'McGoldrick', 'McGrath', 'McGuire', 'McKinley',
        'McMahan', 'McMahon', 'McMath', 'McNally', 'Mcdonald', 'Meade',
        'Meador', 'Mebane', 'Medrano', 'Melton', 'Merchant', 'Merwin',
        'Millam', 'Millard', 'Miller', 'Mills', 'Milstead', 'Minard', 'Miner',
        'Minkoff', 'Minnotte', 'Minyard', 'Mirza', 'Mitchell', 'Money', 'Monk',
        'Montgomery', 'Monton', 'Moore', 'Moren', 'Moreno', 'Morris', 'Morse',
        'Moss', 'Moyer', 'Mueller', 'Mull', 'Mullet', 'Mullins', 'Munn',
        'Murdock', 'Murphey', 'Murphy', 'Murray', 'Murry', 'Mutchler', 'Myers',
        'Myrick', 'Nassar', 'Nathan', 'Nazzal', 'Neal', 'Nederveld', 'Nelson',
        'Nguyen', 'Nichols', 'Nielsen', 'Nockton', 'Nolan', 'Noonan',
        'Norbury', 'Nordlander', 'Norris', 'Norvell', 'Noyes', 'Nugent',
        'Nunn', 'O\'Brien', 'O\'Connell', 'O\'Neill', 'O\'Steen', 'Ober',
        'Odegard', 'Oliver', 'Ollmann', 'Olson', 'Ongley', 'Ordway', 'Ortiz',
        'Ouellette', 'Overcash', 'Overfelt', 'Overley', 'Owens', 'Page',
        'Paige', 'Pardue', 'Parham', 'Parker', 'Parks', 'Patterson', 'Patton',
        'Paul', 'Payne', 'Peck', 'Penisson', 'Percer', 'Perez', 'Perlioni',
        'Perrino', 'Peterman', 'Peters', 'Pfeiffer', 'Phelps', 'Philip',
        'Philippe', 'Phillips', 'Pickett', 'Pippenger', 'Pistole', 'Platzek',
        'Player', 'Poddar', 'Poirier', 'Poklepovic', 'Polk', 'Polking',
        'Pond', 'Popish', 'Porter', 'Pound', 'Pounds', 'Powell', 'Powers',
        'Prado', 'Preston', 'Price', 'Prichep', 'Priour', 'Prischmann',
        'Pryor', 'Puckett', 'Raglin', 'Ralston', 'Rampersad', 'Ratner',
        'Rawles', 'Ray', 'Read', 'Reddy', 'Reed', 'Reese', 'Reeves',
        'Reichenbach', 'Reifel', 'Rein', 'Reiten', 'Reiter', 'Reitmeier',
        'Reynolds', 'Richardson', 'Rider', 'Rhinehart', 'Ritchie',
        'Rittenbach', 'Roberts', 'Robinson', 'Rodriguez', 'Rogers', 'Roper',
        'Rosemblun', 'Rosen', 'Rosenberg', 'Rosenblatt', 'Ross', 'Roth',
        'Rowatt', 'Roy', 'Royston', 'Rozendal', 'Rubble', 'Ruhlin', 'Rupert',
        'Russell', 'Ruthruff', 'Ryan', 'Rye', 'Sabry', 'Sachitano', 'Sachs',
        'Sammartino', 'Sands', 'Saunders', 'Savely', 'Scales', 'Schaefer',
        'Schafer', 'Scheer', 'Schild', 'Schlitt', 'Schmitz', 'Schneider',
        'Schoenberger', 'Schoppe', 'Scott', 'Seay', 'Segura', 'Selesnick',
        'Self', 'Seligmann', 'Sewall', 'Shami', 'Shampine', 'Sharp', 'Shaw',
        'Shefelbine', 'Sheldon', 'Sherrill', 'Shidle', 'Shifley',
        'Shillingsburg', 'Shisler', 'Shopbell', 'Shupack', 'Sievert',
        'Simpson', 'Sims', 'Sissman', 'Smayling', 'Smith', 'Snyder',
        'Solomon', 'Solon', 'Soltero', 'Sommers', 'Sonneborn', 'Sorensen',
        'Southworth', 'Spear', 'Speight', 'Spencer', 'Spruell', 'Spudich',
        'Stacy', 'Staebel', 'Steele', 'Steinhour', 'Steinke', 'Stepp',
        'Stevens', 'Stewart', 'Stickel', 'Stine', 'Stivers', 'Stobb',
        'Stone', 'Stratmann', 'Stubbers', 'Stuckey', 'Stugart', 'Sullivan',
        'Sultan', 'Sumrall', 'Sunley', 'Sunshine', 'Sutton', 'Swaim',
        'Swales', 'Sweed', 'Swick', 'Swift', 'Swindell', 'Swint', 'Symonds',
        'Syzdek', 'Szafranski', 'Takimoto', 'Talbott', 'Talwar', 'Tanner',
        'Taslimi', 'Tate', 'Tatum', 'Taylor', 'Tchainikov', 'Terk',
        'Thacker', 'Thomas', 'Thompson', 'Thomson', 'Thornton', 'Thurman',
        'Thurow', 'Tilley', 'Tolle', 'Towns', 'Trafton', 'Tran', 'Trevas',
        'Trevino', 'Triggs', 'Truchard', 'Tunison', 'Turner', 'Twedell',
        'Tyler', 'Tyree', 'Unger', 'Van', 'Vanderzanden', 'Vanlandingham',
        'Varanasi', 'Varela', 'Varman', 'Venier', 'Verspoor', 'Vick',
        'Visinsky', 'Voltz', 'Wagner', 'Wake', 'Walcott', 'Waldron',
        'Walker', 'Wallace', 'Walters', 'Walton', 'Ward', 'Wardle',
        'Warnes', 'Warren', 'Washington', 'Watson', 'Watters', 'Webber',
        'Weidenfeller', 'Weien', 'Weimer', 'Weiner', 'Weinger', 'Weinheimer',
        'Weirich', 'Welch', 'Wells', 'Wendt', 'West', 'Westmoreland', 'Wex',
        'Whitaker', 'White', 'Whitley', 'Wiediger', 'Wilburn', 'Williams',
        'Williamson', 'Willman', 'Wilson', 'Winger', 'Wise', 'Wisur',
        'Witt', 'Wong', 'Woodbury', 'Wooten', 'Workman', 'Wright', 'Wyatt',
        'Yates', 'Yeamans', 'Yen', 'York', 'Yotov', 'Younan', 'Young',
        'Zeldin', 'Zettner', 'Ziegler', 'Zitterkopf', 'Zucker') ;

    $streetprefixes = array('Main', 'Elm', 'Spruce', 'Oak', 'Pine', '1st',
        '2nd', '3rd', '4th', 'Apple', 'Banana', 'Avocado', 'Mockingbird',
        'Sleepy', 'Oak', 'Sesame', 'Pennsylvania', 'New York', 'New Jersey') ;

    $streetsuffixes = array('Circle', 'Lane', 'Street', 'Road', 'Highway',
        'Hollow', 'Avenue', 'Drive') ;
        
    $citystateziparea = array(
        array('Chicago', 'IL', '60601', '312'),
        array('Denver', 'CO', '80002', '303'),
        array('Los Angeles', 'CA', '90001', '323'),
        array('New York', 'NY', '10001', '212'),
        array('Raleigh', 'NC', '27605', '919'),
        array('Portland', 'OR', '97201', '503'),
        array('Dallas', 'TX', '75201', '214'),
        array('Boston', 'MA', '02101', '617'),
        array('Miami', 'FL', '33010', '305')) ;

    //  How many users?

    $numusers = rand(BULKUSERS_DEMO_MIN_USERS, BULKUSERS_DEMO_MAX_USERS) ;
    //$numusers = 3 ;

    while ($numusers)
    {
        if (rand(0, 1))
            $firstname = $malenames[rand(0, count($malenames) - 1)] ;
        else
            $firstname = $femalenames[rand(0, count($femalenames) - 1)] ;

        $lastname = $surnames[rand(0, count($surnames) - 1)] ;

        //  Create first initial + lastname user login and e-mail address

        $user_pass = wp_generate_password() ;
        $user_login = strtolower(substr($firstname, 0, 1) . $lastname) ;

        //  How should email be constructed?
        if (BULKUSERS_USE_ADMIN_EMAIL_PLUS_SUFFIX)
        {
            $admin_email = explode('@', get_bloginfo('admin_email'), 2) ;
            $user_email = sprintf('%s+%s@%s', $admin_email[0], $user_login, $admin_email[1]) ;
        }
        else
        {
            $user_email = sprintf('%s@%s', $user_login, BULKUSERS_EMAIL_DOMAIN) ;
        }

        //  Only create a user if one doesn't already exist

        if (!username_exists($user_login))
        {
            $user_id = wp_create_user($user_login, $user_pass, $user_email) ;
            //error_log(sprintf('%s::%s:  %s', basename(__FILE__), __LINE__, $user_id)) ;
            if (is_wp_error($user_id))
            {
                $codes = $status->get_error_codes() ;
                error_log(sprintf('%s::%s:  Code:  "%s"  Message:  "%s"',
                    basename(__FILE__), __LINE__, $code[0], $code[1])) ;
                $bulkusers_status[] = sprintf('Bulk Users can\'t create user profile for user:  %s (%s).', $user_login, $user_email) ;
            }

            $userdata = array('ID' => $user_id, 'first_name' => $firstname,
                'last_name' => $lastname, 'nickname' => $firstname,
                'display_name' => $firstname . ' ' . $lastname,
                'user_email' => $user_email) ;
            $status = wp_update_user($userdata) ;

            //error_log(sprintf('%s::%s:  %s', basename(__FILE__), __LINE__, $user_id)) ;
            if (is_wp_error($status))
            {
                $codes = $status->get_error_codes() ;
                error_log(sprintf('%s::%s:  Code:  "%s"  Message:  "%s"',
                    basename(__FILE__), __LINE__, $code[0], $code[1])) ;
                $bulkusers_status[] = sprintf('Bulk Users can\'t update user profile for user:  %s (%s).', $user_login, $user_email) ;
            }
            else
            {
                $bulkusers_count++ ;
                //error_log(sprintf('%s::%s:  %s', basename(__FILE__), __LINE__, $user_id)) ;
                error_log(sprintf('User Created:  %s - %s (%s)', $user_login, $user_email, $user_id)) ;
            }

            //  Need to add the Bulk Users user profile
 
            //$street = rand(100, 9999) ;
            //$street .= " " . $streetprefixes[rand(0, count($streetprefixes) - 1)] ;
            //$street .= " " . $streetsuffixes[rand(0, count($streetsuffixes) - 1)] ;
            //$detail = $citystateziparea[rand(0, count($citystateziparea) - 1)] ;
        }

        $numusers-- ;
    }

    set_transient('bulkusers_count', $bulkusers_count) ;
    set_transient('bulkusers_status', $bulkusers_status) ;
}

function bulkusers_admin_notice() {
    $bulkusers_count = get_transient('bulkusers_count') ;
    $bulkusers_status = get_transient('bulkusers_status') ;

    if (empty($bulkusers_status))
        printf('<div class="updated"><p>Bulk Users created %d new users.</p></div>', $bulkusers_count) ;
    else
        printf('<div class="errors"><p>Bulk Users created %d new users but had errors (see log).</p></div>', $bulkusers_count) ;
}
add_action( 'admin_notices', 'bulkusers_admin_notice' );


/**
 * bulkusers_uninstall - clean up when the plugin is deactivated
 *
 */
function bulkusers_uninstall()
{
}

/**
 *  Activate the plugin initialization function
 */
register_activation_hook(plugin_basename(__FILE__), 'bulkusers_install') ;
register_deactivation_hook(plugin_basename(__FILE__), 'bulkusers_uninstall') ;
?>
