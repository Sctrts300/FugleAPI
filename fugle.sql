-- phpMyAdmin SQL Dump
-- version 5.1.2
-- https://www.phpmyadmin.net/
--
-- Vært: localhost:3306
-- Genereringstid: 31. 10 2025 kl. 11:29:27
-- Serverversion: 5.7.24
-- PHP-version: 8.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fugle`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `birbs`
--

CREATE TABLE `birbs` (
  `id` int(20) NOT NULL,
  `name` varchar(35) NOT NULL,
  `habitat` text NOT NULL,
  `diet` text NOT NULL,
  `weight_in_grams` int(11) NOT NULL,
  `wingspan_in_meters` decimal(6,2) NOT NULL,
  `features` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `birbs`
--

INSERT INTO `birbs` (`id`, `name`, `habitat`, `diet`, `weight_in_grams`, `wingspan_in_meters`, `features`) VALUES
(1, 'Shoebill', 'Swamps of central tropical Africa', 'Fish (especially lungfish), frogs, small reptiles', 4000, '2.10', 'Massive shoe-shaped bill, dinosaur-like posture, slow movements'),
(2, 'Andean Cock-of-the-Rock', 'Cloud forests of the Andes (South America)', 'Fruits, insects, small vertebrates', 230, '0.65', 'Bright red-orange plumage, large half-disc crest, loud display calls'),
(3, 'Bearded Vulture', 'Mountainous regions of Europe, Africa, and Asia', 'Mostly bones (up to 85%), small mammals', 4500, '2.30', 'Orange-stained feathers, drops bones to crack them, bearded face'),
(4, 'Blue-footed Booby', 'Coastal areas of the eastern Pacific (Galápagos, Peru, Mexico)', 'Fish, squid', 1, '1.50', 'Bright blue feet, elaborate mating dances, dives for prey'),
(5, 'Club-winged Manakin', 'Cloud forests of Ecuador and Colombia', 'Fruits and small insects', 15, '0.09', 'Makes violin-like sounds by rubbing wings together'),
(6, 'Frigatebird', 'Tropical and subtropical oceans', 'Fish, squid, stolen food from other birds', 1000, '2.10', 'Huge red throat pouch in males, long forked tail, cannot land on water'),
(7, 'Great Potoo', 'Tropical forests of Central and South America', 'Large flying insects, small bats', 360, '0.70', 'Camouflaged as tree bark, haunting vocalizations, nocturnal'),
(8, 'Greater Sage-Grouse', 'Sagebrush plains of western North America', 'Leaves, buds, insects', 1, '0.90', 'Males inflate yellow air sacs and make popping sounds in displays'),
(9, 'Guianan Cock-of-the-Rock', 'Rainforests of northern South America', 'Fruits, insects, small reptiles', 200, '0.65', 'Bright orange plumage, half-moon crest, communal courtship displays'),
(10, 'Harpy Eagle', 'Tropical rainforests of Central and South America', 'Monkeys, sloths, large birds', 6000, '1.80', 'Massive claws, crown-like feathers, incredibly powerful hunter'),
(11, 'Helmeted Hornbill', 'Rainforests of Southeast Asia', 'Fruits (mainly figs), small animals', 3000, '1.30', 'Solid casque used for aerial headbutting, eerie laughing call'),
(12, 'Hoatzin', 'Swamps and river forests of the Amazon Basin', 'Leaves (fermenting gut), fruits, flowers', 700, '0.65', 'Ferments food like a cow, smells bad, chicks have clawed wings'),
(13, 'Inca Tern', 'Coastal cliffs of Peru and Chile', 'Fish, planktonic crustaceans', 180, '0.64', 'Gray body with elegant white mustache, bright red beak and feet'),
(14, 'Kakapo', 'Forests of New Zealand', 'Seeds, fruits, leaves, roots', 2000, '0.00', 'Flightless nocturnal parrot, sweet smell, booming mating calls'),
(15, 'King of Saxony Bird-of-Paradise', 'Mountain forests of New Guinea', 'Fruits, insects', 70, '0.70', 'Two extremely long head plumes used in mating displays'),
(16, 'Long-wattled Umbrellabird', 'Rainforests of Colombia and Ecuador', 'Fruits, insects, small vertebrates', 400, '0.66', 'Large hanging throat wattle, umbrella-like crest'),
(17, 'Marabou Stork', 'Savannas and wetlands of sub-Saharan Africa', 'Carrion, garbage, fish, small animals', 4000, '2.60', 'Bare head, pink throat pouch, scavenger habits, called \'undertaker bird\''),
(18, 'Secretary Bird', 'Grasslands and savannas of sub-Saharan Africa', 'Snakes, insects, small mammals', 3, '2.30', 'Long legs, hunts by stomping prey, quill-like feathers on head'),
(19, 'Smew', 'Northern Europe and Asia (lakes, rivers, wetlands)', 'Fish, aquatic invertebrates', 450, '0.56', 'Striking black-and-white plumage, looks like a \'panda duck\''),
(20, 'Superb Bird-of-Paradise', 'Rainforests of New Guinea', 'Fruits, insects', 70, '0.25', 'Transforms into a black oval with blue highlights during courtship');

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `birb_media`
--

CREATE TABLE `birb_media` (
  `birb_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `birb_media`
--

INSERT INTO `birb_media` (`birb_id`, `media_id`) VALUES
(1, 1),
(2, 2),
(3, 3),
(4, 4),
(5, 5),
(6, 6),
(7, 7),
(8, 8),
(9, 9),
(10, 10),
(11, 11),
(12, 12),
(13, 13),
(14, 14),
(15, 15),
(16, 16),
(17, 17),
(18, 18),
(19, 19),
(20, 20);

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `name` varchar(35) NOT NULL,
  `url` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Data dump for tabellen `media`
--

INSERT INTO `media` (`id`, `name`, `url`) VALUES
(1, 'Shoebill', 'https://www.stancsmith.com/uploads/4/8/9/6/48964465/11fb316ea314132d049292ff9995cfd77be97a0c_orig.jpeg'),
(2, 'Andean-Cock-of-the-rock', 'https://www.birdnote.org/sites/default/files/Andean-Cock-of-the-rock-800-Nathan-Rupert-CC.jpg'),
(3, 'Bearded-Vulture', 'https://upload.wikimedia.org/wikipedia/commons/c/cf/010e_Wild_Bearded_Vulture_in_flight_at_Pfyn-Finges_%28Switzerland%29_Photo_by_Giles_Laurent.jpg'),
(4, 'Blue‑footed-Booby', 'https://good-nature-blog-uploads.s3.amazonaws.com/uploads/2015/08/Blue-Footed-Booby-dance-flickr.com_.jpg'),
(5, 'Club‑winged-Manakin', 'https://cdn.download.ams.birds.cornell.edu/api/v1/asset/244156621/320'),
(6, 'Frigatebird', 'https://ask-nature.sfo3.digitaloceanspaces.com/wp-content/uploads/2020/08/31121916/Male-Frigate_stirwise-2160x1440.png'),
(7, 'Great-Potoo', 'https://abcbirds.org/wp-content/uploads/2019/10/Fabio-Maffei-SS.jpg'),
(8, 'Greater-Sage‐Grouse', 'https://www.allaboutbirds.org/guide/assets/photo/37805841-480px.jpg'),
(9, 'Guianan-Cock‑of‑the‑Rock', 'https://upload.wikimedia.org/wikipedia/commons/4/48/Guianan_Cock-of-the-rock_%28Rupicola_rupicola%29.jpg'),
(10, 'Harpy-Eagle', 'https://images.squarespace-cdn.com/content/v1/657b302ad0d11e71b22b40c3/1705331492150-61YPVE4MIJK2NRB21DZR/preview_9_vgKUGgi.normal.jpg'),
(11, 'Helmeted-Hornbill', 'https://www.rainforesttrust.org/app/uploads/2024/10/Helmeted-Hornbill_Craig-Ansibin-shutterstock_1225888735-scaled-aspect-ratio-1920-1300.jpg'),
(12, 'Hoatzin', 'https://cdn.download.ams.birds.cornell.edu/api/v1/asset/26475981/640'),
(13, 'Inca-Tern', 'https://upload.wikimedia.org/wikipedia/commons/9/93/Larosterna_inca_-Lima%2C_Peru_-adult-8.jpg'),
(14, 'Kakapo', 'https://media.wired.com/photos/59326c6058b0d64bb35d1809/master/pass/Kakapo-2.jpg'),
(15, 'King-of-Saxony-Bird‑of‑Paradise', 'https://i.ytimg.com/vi/MdNyeasi0GI/maxresdefault.jpg'),
(16, 'Long‑wattled-Umbrellabird', 'https://b3123118.smushcdn.com/3123118/wp-content/uploads/2019/04/DSC_5631-2.jpg?lossy=1&strip=1&webp=1'),
(17, 'Marabou-Stork', 'https://upload.wikimedia.org/wikipedia/commons/0/05/Marabou_stork_%28Leptoptilos_crumenifer%29.jpg'),
(18, 'Secretary-Bird', 'https://upload.wikimedia.org/wikipedia/commons/6/66/Secretary_bird_Mara_for_WC.jpg'),
(19, 'Smew', 'https://www.alaskasealife.org//uploads/animals/images/male_and_female_smew.jpg'),
(20, 'Superb-Bird‑of‑Paradise', 'https://i.natgeofe.com/n/6f9b6d9e-5797-4867-a859-7b0c2a66cd3b/02-bird-of-paradise-A012_C010_1029SF_0001575.jpg');

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `birbs`
--
ALTER TABLE `birbs`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `birb_media`
--
ALTER TABLE `birb_media`
  ADD KEY `fk_birb_id` (`birb_id`),
  ADD KEY `fk_media_id` (`media_id`);

--
-- Indeks for tabel `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `birbs`
--
ALTER TABLE `birbs`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Tilføj AUTO_INCREMENT i tabel `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `birb_media`
--
ALTER TABLE `birb_media`
  ADD CONSTRAINT `fk_birb_id` FOREIGN KEY (`birb_id`) REFERENCES `birbs` (`id`),
  ADD CONSTRAINT `fk_media_id` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
