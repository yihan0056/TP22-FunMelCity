CREATE TABLE `casual_salary` (
  `job` varchar(100) NOT NULL,
  `salary_rank` varchar(50) NOT NULL,
  `75_earn_more` int(6) DEFAULT NULL,
  `50_earn_more` int(6) NOT NULL,
  `25_earn_more` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

ALTER TABLE `casual_salary`
  ADD PRIMARY KEY (`job`);
COMMIT;



CREATE TABLE `part_time_salary` (
  `Company` varchar(200) NOT NULL,
  `job title` varchar(100) NOT NULL,
  `salary` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;



CREATE TABLE `mel_rental_price` (
  `districrt` varchar(100) DEFAULT NULL,
  `surburb` varchar(200) DEFAULT NULL,
  `room_type` varchar(100) DEFAULT NULL,
  `percentile_25` decimal(6,2) DEFAULT NULL,
  `median` decimal(6,2) DEFAULT NULL,
  `percentile_75` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
COMMIT;



CREATE TABLE `mel_product_price` (
  `item` varchar(100) NOT NULL,
  `price` decimal(6,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `mel_product_price`
  ADD PRIMARY KEY (`item`);
COMMIT;