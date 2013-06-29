-- dudu_passenger
CREATE TABLE dudu_passenger
(
	pid serial NOT NULL,
	username char(11) NOT NULL,
	passwd char(32),
	email varchar(64),
	nickname varchar(32) NOT NULL,
	gender	boolean NOT NULL DEFAULT true, -- true as male,false as female
	mobile_id varchar(64) NOT NULL,
	reg_time timestamp without time zone,
	reg_ip inet,
	last_login_time timestamp without time zone,
	last_login_ip inet,
	"last_login_position" geometry(Point,4326),
	status smallint NOT NULL,
	more_info varchar(1024),
	CONSTRAINT passenger_pkey PRIMARY KEY (pid)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_passenger
  OWNER TO dudu;
CREATE UNIQUE INDEX p_username ON dudu_passenger (username);
CREATE INDEX p_status ON dudu_passenger (status);

-- dudu_driver
CREATE TABLE dudu_driver
(
	did serial NOT NULL,
	username char(11) NOT NULL,
	passwd char(32),
	email varchar(64),
	truename varchar(32) NOT NULL,
	nickname varchar(12) NOT NULL, 
	mobile_id varchar(64) NOT NULL,
	car_number char(11) NOT NULL,
	taxi_company_id smallint NOT NULL,
	driver_number varchar(10) NOT NULL,
	city_id smallint NOT NULL,
	rec_username varchar(11),
	reg_time timestamp without time zone,
	reg_ip inet,
	last_login_time timestamp without time zone,
	last_login_ip inet,
	"last_login_position" geometry(Point,4326),
	status smallint NOT NULL,
	more_info varchar(1024),
	CONSTRAINT driver_dkey PRIMARY KEY (did)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_driver
  OWNER TO dudu;
CREATE UNIQUE INDEX d_username ON dudu_driver (username);
CREATE INDEX d_car_number ON dudu_driver (car_number);
CREATE INDEX d_taxi_company_id ON dudu_driver (taxi_company_id);
CREATE INDEX d_driver_city_id ON dudu_driver (city_id);
CREATE INDEX d_rec_username ON dudu_driver (rec_username);
CREATE INDEX d_status ON dudu_driver (status);

-- dudu_order_normal
CREATE TABLE dudu_order_normal
(
	order_id serial NOT NULL,
	pid integer NOT NULL,
	did integer NOT NULL,
	passenger_position  geometry(Point,4326),
	driver_position  geometry(Point,4326),
	status smallint NOT NULL,
	request_time timestamp without time zone,
	reply_time timestamp without time zone,
	start_point varchar(64) NOT NULL,
	end_point varchar(64),
	driver_leave_time timestamp without time zone,
	passenger_rided_time timestamp without time zone,
	ride_position  geometry(Point,4326),
	evaluate smallint DEFAULT 0 NOT NULL,
	voice_url varchar(64),
	CONSTRAINT order_normal_okey PRIMARY KEY (order_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_order_normal
  OWNER TO dudu;
CREATE INDEX o_pid ON dudu_order_normal (pid);
CREATE INDEX o_did ON dudu_order_normal (did);
CREATE INDEX o_request_time ON dudu_order_normal (request_time);
CREATE INDEX o_status ON dudu_order_normal (status);
CREATE INDEX o_evaluate ON dudu_order_normal (evaluate);

-- dudu_order_reserve
CREATE TABLE dudu_order_reserve
(
	order_id serial NOT NULL,
	pid integer NOT NULL,
	did integer NOT NULL,
	passenger_position  geometry(Point,4326),
	driver_position  geometry(Point,4326),
	status smallint NOT NULL,
	request_time timestamp without time zone,
	reply_time timestamp without time zone,
	use_time timestamp without time zone,
	valid_time timestamp without time zone,
	start_point varchar(64) NOT NULL,
	end_point varchar(64),
	driver_leave_time timestamp without time zone,
	passenger_rided_time timestamp without time zone,
	ride_position  geometry(Point,4326),
	evaluate smallint DEFAULT 0 NOT NULL,
	voice_url varchar(64),
	CONSTRAINT order_reserve_okey PRIMARY KEY (order_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_order_reserve
  OWNER TO dudu;
CREATE INDEX order_reserve__pid ON dudu_order_reserve (pid);
CREATE INDEX order_reserve__did ON dudu_order_reserve (did);
CREATE INDEX order_reserve__request_time ON dudu_order_reserve (request_time);
CREATE INDEX order_reserve__status ON dudu_order_reserve (status);
CREATE INDEX order_reserve__evaluate ON dudu_order_reserve (evaluate);

-- dudu_passenger_order
CREATE TABLE dudu_passenger_order
(
	pid integer NOT NULL,
	all_num integer DEFAULT 0 NOT NULL,
	success_num integer DEFAULT 0 NOT NULL,
	fail_num smallint DEFAULT 0 NOT NULL,
	broke_num smallint DEFAULT 0 NOT NULL,
	CONSTRAINT passenger_order_okey PRIMARY KEY (pid)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_passenger_order
  OWNER TO dudu;
  
-- dudu_driver_order
CREATE TABLE dudu_driver_order
(
	did integer NOT NULL,
	all_num integer DEFAULT 0 NOT NULL,
	success_num integer DEFAULT 0 NOT NULL,
	fail_num smallint DEFAULT 0 NOT NULL,
	broke_num smallint DEFAULT 0 NOT NULL,
	m_num smallint DEFAULT 0 NOT NULL,
	m_success_num smallint DEFAULT 0 NOT NULL,
	m_fail_num smallint DEFAULT 0 NOT NULL,
	m_broke_num smallint DEFAULT 0 NOT NULL,
	CONSTRAINT driver_order_dkey PRIMARY KEY (did)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_driver_order
  OWNER TO dudu;
CREATE INDEX all_num ON dudu_driver_order (all_num);
CREATE INDEX m_num ON dudu_driver_order (m_num);


-- dudu_order_path
CREATE TABLE dudu_order_path
(
	order_id integer NOT NULL,
	path_info text,
	CONSTRAINT order_path_dkey PRIMARY KEY (order_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_order_path
  OWNER TO dudu;

  
-- dudu_order_evaluate
CREATE TABLE dudu_order_evaluate
(
	order_id integer NOT NULL,
	pid integer NOT NULL,
	did integer NOT NULL,
	cause smallint DEFAULT 0 NOT NULL,
	taxi_type smallint DEFAULT 0 NOT NULL,
	more_info varchar(1024),
	CONSTRAINT order_evaluate_okey PRIMARY KEY (order_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_order_evaluate
  OWNER TO dudu;
  
  
-- dudu_taxi_company
CREATE TABLE dudu_taxi_company
(
	taxi_company_id serial NOT NULL,
	taxi_company_name varchar(32) NOT NULL,
	taxi_company_full_name varchar(64) NOT NULL,
	city_id smallint NOT NULL,
	CONSTRAINT taxi_company_tkey PRIMARY KEY (taxi_company_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_taxi_company
  OWNER TO dudu;
CREATE INDEX city_id ON dudu_taxi_company (city_id);
CREATE UNIQUE INDEX taxi_company_name ON dudu_taxi_company (taxi_company_name);


-- dudu_province
CREATE TABLE dudu_province
(
	province_id serial NOT NULL,
	province_name varchar(64) NOT NULL,
	CONSTRAINT province_pkey PRIMARY KEY (province_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_province
  OWNER TO dudu;
  
-- dudu_city
CREATE TABLE dudu_city
(
	city_id serial NOT NULL,
	city_name varchar(64) NOT NULL,
	province_id integer NOT NULL,
	status smallint NOT NULL,
	CONSTRAINT city_pkey PRIMARY KEY (city_id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_city
  OWNER TO dudu;
CREATE INDEX province_status ON dudu_city (province_id,status);
  
-- dudu_passenger_vercode
CREATE TABLE dudu_passenger_vercode
(
	username char(11) NOT NULL,
	code varchar(64) NOT NULL,
	valid_time integer NOT NULL
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_passenger_vercode
  OWNER TO dudu;
CREATE UNIQUE INDEX username ON dudu_passenger_vercode (username);
CREATE INDEX select_all ON dudu_passenger_vercode (username,valid_time);

-- dudu_driver_vercode
CREATE TABLE dudu_driver_vercode
(
	username char(11) NOT NULL,
	code varchar(64) NOT NULL,
	valid_time integer NOT NULL
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_driver_vercode
  OWNER TO dudu;
CREATE UNIQUE INDEX d_v_username ON dudu_driver_vercode (username);
CREATE INDEX d_v_select_all ON dudu_driver_vercode (username,valid_time);

-- dudu_passenger_data
CREATE TABLE dudu_passenger_data
(
	pid integer NOT NULL,
	token varchar(64) NOT NULL,
	position geometry(Point,4326),
	update_time timestamp without time zone,
	CONSTRAINT passenger_data_pkey PRIMARY KEY (pid)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_passenger_data
  OWNER TO dudu;
  
-- dudu_driver_data
CREATE TABLE dudu_driver_data
(
	pid integer NOT NULL,
	token varchar(64) NOT NULL,
	position geometry(Point,4326),
	status smallint default 0 NOT NULL,
	speed numeric(8,3),
	heading numeric(8,3),
	update_time timestamp without time zone,
	CONSTRAINT driver_data_pkey PRIMARY KEY (pid)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_driver_data
  OWNER TO dudu;
  
  
-- dudu_compliant
CREATE TABLE dudu_compliant
(
	id serial NOT NULL,
	c_type smallint,
	content varchar(1024) NOT NULL,
	user_type smallint default 0 NOT NULL,
	user_id integer NOT NULL,
	add_time timestamp without time zone,
	CONSTRAINT compliant_key PRIMARY KEY (id)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_compliant
  OWNER TO dudu;
CREATE INDEX user_id ON dudu_compliant (user_id);

-- dudu_token
CREATE TABLE dudu_passenger_token
(
	pid integer NOT NULL,
	token char(32) NOT NULL,
	CONSTRAINT passenger_token_pid PRIMARY KEY (pid)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_passenger_token
  OWNER TO dudu;
  
  
-- dudu_driver_token
CREATE TABLE dudu_driver_token
(
	did integer NOT NULL,
	token char(32) NOT NULL,
	CONSTRAINT dudu_driver_did PRIMARY KEY (did)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_driver_token
  OWNER TO dudu;
  
-- dudu_passenger_position
CREATE TABLE dudu_passenger_position
(
	pid integer NOT NULL,
	username char(11),
	location geometry(Point,4326),
	alti numeric(8,3),
	speed numeric(5,2),
	direction numeric(5,2),
	accuracy smallint,
	sate_num smallint,
	gps_timestamp integer,
	address varchar(128),
	status smallint default 1 NOT NULL,
	CONSTRAINT dudu_passenger_position_pid PRIMARY KEY (pid)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_passenger_position
  OWNER TO dudu;
CREATE INDEX p_position_pid ON dudu_passenger_position (pid);
  
-- dudu_driver_position
CREATE TABLE dudu_driver_position
(
	did integer NOT NULL,
	username char(11),
	location geometry(Point,4326),
	alti numeric(8,3),
	speed numeric(5,2),
	direction numeric(5,2),
	accuracy smallint,
	sate_num smallint,
	gps_timestamp integer,
	address varchar(128),
	status smallint default 1 NOT NULL,
	CONSTRAINT dudu_driver_position_did PRIMARY KEY (did)
)
WITH (
  OIDS=FALSE
);
ALTER TABLE dudu_driver_position
  OWNER TO dudu;
CREATE INDEX d_position_did ON dudu_driver_position (did);