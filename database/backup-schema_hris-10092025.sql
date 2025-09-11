--
-- PostgreSQL database cluster dump
--

-- Started on 2025-09-10 20:05:08

SET default_transaction_read_only = off;

SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;

--
-- Roles
--

CREATE ROLE ict;
ALTER ROLE ict WITH SUPERUSER INHERIT CREATEROLE CREATEDB LOGIN NOREPLICATION BYPASSRLS PASSWORD 'SCRAM-SHA-256$4096:LJFUL6Gbb2oXoOvuH1oJ1A==$aSLiBpxG+3mhsrn8FKKptuFY+Iwwkjr37eBtj9LnAHA=:pKHPDqZevZByizaH1tTZmXdJozTt5omVxtU6v+a4tQE=';
CREATE ROLE postgres;
ALTER ROLE postgres WITH SUPERUSER INHERIT CREATEROLE CREATEDB LOGIN REPLICATION BYPASSRLS PASSWORD 'SCRAM-SHA-256$4096:FCrkA2Q31uTxWQN17yUQdg==$laX8qp5bLZMfVkh82QqhZpaZbnb5L7tSbcH6Xzcmj0E=:TyGIfuNWeX/tze4jX2fQZR3PU8cedSt7m8kTLNoL5ls=';

--
-- User Configurations
--








--
-- Databases
--

--
-- Database "template1" dump
--

\connect template1

--
-- PostgreSQL database dump
--

-- Dumped from database version 16.9
-- Dumped by pg_dump version 16.9

-- Started on 2025-09-10 20:05:08

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

-- Completed on 2025-09-10 20:05:09

--
-- PostgreSQL database dump complete
--

--
-- Database "db_hris" dump
--

--
-- PostgreSQL database dump
--

-- Dumped from database version 16.9
-- Dumped by pg_dump version 16.9

-- Started on 2025-09-10 20:05:09

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 5575 (class 1262 OID 16397)
-- Name: db_hris; Type: DATABASE; Schema: -; Owner: ict
--

CREATE DATABASE db_hris WITH TEMPLATE = template0 ENCODING = 'UTF8' LOCALE_PROVIDER = libc LOCALE = 'English_United States.1252';


ALTER DATABASE db_hris OWNER TO ict;

\connect db_hris

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

SET default_tablespace = '';

SET default_table_access_method = heap;

--
-- TOC entry 215 (class 1259 OID 16399)
-- Name: activity_log; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.activity_log (
    id bigint NOT NULL,
    log_name character varying(255),
    description text NOT NULL,
    subject_type character varying(255),
    subject_id bigint,
    causer_type character varying(255),
    causer_id bigint,
    properties json,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    event character varying(255),
    batch_uuid uuid
);


ALTER TABLE public.activity_log OWNER TO ict;

--
-- TOC entry 216 (class 1259 OID 16404)
-- Name: activity_log_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.activity_log_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.activity_log_id_seq OWNER TO ict;

--
-- TOC entry 5576 (class 0 OID 0)
-- Dependencies: 216
-- Name: activity_log_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.activity_log_id_seq OWNED BY public.activity_log.id;


--
-- TOC entry 217 (class 1259 OID 16405)
-- Name: approval_cutis; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.approval_cutis (
    id_approval_cuti integer NOT NULL,
    cuti_id integer NOT NULL,
    checked1_for integer NOT NULL,
    checked1_by integer,
    checked1_karyawan_id character varying(255),
    checked2_for integer NOT NULL,
    checked2_by integer,
    checked2_karyawan_id character varying(255),
    approved_for integer NOT NULL,
    approved_by integer,
    approved_karyawan_id character varying(255),
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.approval_cutis OWNER TO ict;

--
-- TOC entry 218 (class 1259 OID 16410)
-- Name: approval_cutis_id_approval_cuti_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.approval_cutis_id_approval_cuti_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.approval_cutis_id_approval_cuti_seq OWNER TO ict;

--
-- TOC entry 5577 (class 0 OID 0)
-- Dependencies: 218
-- Name: approval_cutis_id_approval_cuti_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.approval_cutis_id_approval_cuti_seq OWNED BY public.approval_cutis.id_approval_cuti;


--
-- TOC entry 219 (class 1259 OID 16411)
-- Name: attachment_ksk_details; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.attachment_ksk_details (
    id_attachment_ksk_detail bigint NOT NULL,
    ksk_detail_id integer NOT NULL,
    path character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.attachment_ksk_details OWNER TO ict;

--
-- TOC entry 220 (class 1259 OID 16414)
-- Name: attachment_ksk_details_id_attachment_ksk_detail_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.attachment_ksk_details_id_attachment_ksk_detail_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attachment_ksk_details_id_attachment_ksk_detail_seq OWNER TO ict;

--
-- TOC entry 5578 (class 0 OID 0)
-- Dependencies: 220
-- Name: attachment_ksk_details_id_attachment_ksk_detail_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.attachment_ksk_details_id_attachment_ksk_detail_seq OWNED BY public.attachment_ksk_details.id_attachment_ksk_detail;


--
-- TOC entry 221 (class 1259 OID 16415)
-- Name: attachment_lemburs; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.attachment_lemburs (
    id_attachment_lembur integer NOT NULL,
    lembur_id character varying(255) NOT NULL,
    path character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.attachment_lemburs OWNER TO ict;

--
-- TOC entry 222 (class 1259 OID 16420)
-- Name: attachment_lemburs_id_attachment_lembur_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.attachment_lemburs_id_attachment_lembur_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attachment_lemburs_id_attachment_lembur_seq OWNER TO ict;

--
-- TOC entry 5579 (class 0 OID 0)
-- Dependencies: 222
-- Name: attachment_lemburs_id_attachment_lembur_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.attachment_lemburs_id_attachment_lembur_seq OWNED BY public.attachment_lemburs.id_attachment_lembur;


--
-- TOC entry 223 (class 1259 OID 16421)
-- Name: attendance_devices; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.attendance_devices (
    id_device integer NOT NULL,
    organisasi_id integer NOT NULL,
    cloud_id character varying(255) NOT NULL,
    device_sn character varying(255) NOT NULL,
    device_name character varying(255) NOT NULL,
    server_ip character varying(255) NOT NULL,
    server_port character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.attendance_devices OWNER TO ict;

--
-- TOC entry 224 (class 1259 OID 16426)
-- Name: attendance_devices_id_device_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.attendance_devices_id_device_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attendance_devices_id_device_seq OWNER TO ict;

--
-- TOC entry 5580 (class 0 OID 0)
-- Dependencies: 224
-- Name: attendance_devices_id_device_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.attendance_devices_id_device_seq OWNED BY public.attendance_devices.id_device;


--
-- TOC entry 225 (class 1259 OID 16427)
-- Name: attendance_gps; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.attendance_gps (
    id_att_gps integer NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    divisi_id integer,
    pin character varying(255) NOT NULL,
    latitude character varying(255) NOT NULL,
    longitude character varying(255) NOT NULL,
    attendance_date date NOT NULL,
    attendance_time timestamp(0) without time zone NOT NULL,
    attachment character varying(255) NOT NULL,
    type character varying(2) NOT NULL,
    status character varying(255) NOT NULL,
    scanlog_id integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT attendance_gps_status_check CHECK (((status)::text = ANY (ARRAY[('IN'::character varying)::text, ('OUT'::character varying)::text])))
);


ALTER TABLE public.attendance_gps OWNER TO ict;

--
-- TOC entry 226 (class 1259 OID 16433)
-- Name: attendance_gps_id_att_gps_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.attendance_gps_id_att_gps_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attendance_gps_id_att_gps_seq OWNER TO ict;

--
-- TOC entry 5581 (class 0 OID 0)
-- Dependencies: 226
-- Name: attendance_gps_id_att_gps_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.attendance_gps_id_att_gps_seq OWNED BY public.attendance_gps.id_att_gps;


--
-- TOC entry 227 (class 1259 OID 16434)
-- Name: attendance_karyawan_grup; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.attendance_karyawan_grup (
    id bigint NOT NULL,
    karyawan_id character varying(255),
    organisasi_id integer NOT NULL,
    grup_id integer NOT NULL,
    active_date timestamp(0) without time zone NOT NULL,
    toleransi_waktu time(0) without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    jam_masuk time(0) without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    jam_keluar time(0) without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    pin character varying(255)
);


ALTER TABLE public.attendance_karyawan_grup OWNER TO ict;

--
-- TOC entry 228 (class 1259 OID 16442)
-- Name: attendance_karyawan_grup_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.attendance_karyawan_grup_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attendance_karyawan_grup_id_seq OWNER TO ict;

--
-- TOC entry 5582 (class 0 OID 0)
-- Dependencies: 228
-- Name: attendance_karyawan_grup_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.attendance_karyawan_grup_id_seq OWNED BY public.attendance_karyawan_grup.id;


--
-- TOC entry 229 (class 1259 OID 16443)
-- Name: attendance_scanlogs; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.attendance_scanlogs (
    id_scanlog integer NOT NULL,
    organisasi_id integer NOT NULL,
    device_id integer NOT NULL,
    start_date_scan date NOT NULL,
    end_date_scan date NOT NULL,
    scan_date timestamp(0) without time zone NOT NULL,
    scan_status integer NOT NULL,
    verify integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    pin character varying(255)
);


ALTER TABLE public.attendance_scanlogs OWNER TO ict;

--
-- TOC entry 230 (class 1259 OID 16446)
-- Name: attendance_scanlogs_id_scanlog_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.attendance_scanlogs_id_scanlog_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attendance_scanlogs_id_scanlog_seq OWNER TO ict;

--
-- TOC entry 5583 (class 0 OID 0)
-- Dependencies: 230
-- Name: attendance_scanlogs_id_scanlog_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.attendance_scanlogs_id_scanlog_seq OWNED BY public.attendance_scanlogs.id_scanlog;


--
-- TOC entry 231 (class 1259 OID 16447)
-- Name: attendance_summaries; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.attendance_summaries (
    id_att_summary bigint NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    pin character varying(255) NOT NULL,
    periode date NOT NULL,
    organisasi_id integer NOT NULL,
    divisi_id integer,
    departemen_id integer,
    seksi_id integer,
    jabatan_id integer,
    total_absen integer DEFAULT 0 NOT NULL,
    total_sakit integer DEFAULT 0 NOT NULL,
    total_izin integer DEFAULT 0 NOT NULL,
    total_hadir integer DEFAULT 0 NOT NULL,
    keterlambatan integer DEFAULT 0 NOT NULL,
    is_cutoff character varying(1) DEFAULT 'N'::character varying NOT NULL,
    tanggal1_selisih integer DEFAULT 0 NOT NULL,
    tanggal1_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal1_in character varying(255),
    tanggal1_out character varying(255),
    tanggal2_selisih integer DEFAULT 0 NOT NULL,
    tanggal2_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal2_in character varying(255),
    tanggal2_out character varying(255),
    tanggal3_selisih integer DEFAULT 0 NOT NULL,
    tanggal3_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal3_in character varying(255),
    tanggal3_out character varying(255),
    tanggal4_selisih integer DEFAULT 0 NOT NULL,
    tanggal4_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal4_in character varying(255),
    tanggal4_out character varying(255),
    tanggal5_selisih integer DEFAULT 0 NOT NULL,
    tanggal5_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal5_in character varying(255),
    tanggal5_out character varying(255),
    tanggal6_selisih integer DEFAULT 0 NOT NULL,
    tanggal6_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal6_in character varying(255),
    tanggal6_out character varying(255),
    tanggal7_selisih integer DEFAULT 0 NOT NULL,
    tanggal7_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal7_in character varying(255),
    tanggal7_out character varying(255),
    tanggal8_selisih integer DEFAULT 0 NOT NULL,
    tanggal8_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal8_in character varying(255),
    tanggal8_out character varying(255),
    tanggal9_selisih integer DEFAULT 0 NOT NULL,
    tanggal9_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal9_in character varying(255),
    tanggal9_out character varying(255),
    tanggal10_selisih integer DEFAULT 0 NOT NULL,
    tanggal10_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal10_in character varying(255),
    tanggal10_out character varying(255),
    tanggal11_selisih integer DEFAULT 0 NOT NULL,
    tanggal11_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal11_in character varying(255),
    tanggal11_out character varying(255),
    tanggal12_selisih integer DEFAULT 0 NOT NULL,
    tanggal12_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal12_in character varying(255),
    tanggal12_out character varying(255),
    tanggal13_selisih integer DEFAULT 0 NOT NULL,
    tanggal13_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal13_in character varying(255),
    tanggal13_out character varying(255),
    tanggal14_selisih integer DEFAULT 0 NOT NULL,
    tanggal14_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal14_in character varying(255),
    tanggal14_out character varying(255),
    tanggal15_selisih integer DEFAULT 0 NOT NULL,
    tanggal15_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal15_in character varying(255),
    tanggal15_out character varying(255),
    tanggal16_selisih integer DEFAULT 0 NOT NULL,
    tanggal16_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal16_in character varying(255),
    tanggal16_out character varying(255),
    tanggal17_selisih integer DEFAULT 0 NOT NULL,
    tanggal17_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal17_in character varying(255),
    tanggal17_out character varying(255),
    tanggal18_selisih integer DEFAULT 0 NOT NULL,
    tanggal18_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal18_in character varying(255),
    tanggal18_out character varying(255),
    tanggal19_selisih integer DEFAULT 0 NOT NULL,
    tanggal19_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal19_in character varying(255),
    tanggal19_out character varying(255),
    tanggal20_selisih integer DEFAULT 0 NOT NULL,
    tanggal20_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal20_in character varying(255),
    tanggal20_out character varying(255),
    tanggal21_selisih integer DEFAULT 0 NOT NULL,
    tanggal21_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal21_in character varying(255),
    tanggal21_out character varying(255),
    tanggal22_selisih integer DEFAULT 0 NOT NULL,
    tanggal22_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal22_in character varying(255),
    tanggal22_out character varying(255),
    tanggal23_selisih integer DEFAULT 0 NOT NULL,
    tanggal23_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal23_in character varying(255),
    tanggal23_out character varying(255),
    tanggal24_selisih integer DEFAULT 0 NOT NULL,
    tanggal24_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal24_in character varying(255),
    tanggal24_out character varying(255),
    tanggal25_selisih integer DEFAULT 0 NOT NULL,
    tanggal25_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal25_in character varying(255),
    tanggal25_out character varying(255),
    tanggal26_selisih integer DEFAULT 0 NOT NULL,
    tanggal26_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal26_in character varying(255),
    tanggal26_out character varying(255),
    tanggal27_selisih integer DEFAULT 0 NOT NULL,
    tanggal27_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal27_in character varying(255),
    tanggal27_out character varying(255),
    tanggal28_selisih integer DEFAULT 0 NOT NULL,
    tanggal28_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal28_in character varying(255),
    tanggal28_out character varying(255),
    tanggal29_selisih integer DEFAULT 0 NOT NULL,
    tanggal29_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal29_in character varying(255),
    tanggal29_out character varying(255),
    tanggal30_selisih integer DEFAULT 0 NOT NULL,
    tanggal30_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal30_in character varying(255),
    tanggal30_out character varying(255),
    tanggal31_selisih integer DEFAULT 0 NOT NULL,
    tanggal31_status character varying(1) DEFAULT 'A'::character varying NOT NULL,
    tanggal31_in character varying(255),
    tanggal31_out character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.attendance_summaries OWNER TO ict;

--
-- TOC entry 232 (class 1259 OID 16520)
-- Name: attendance_summaries_id_att_summary_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.attendance_summaries_id_att_summary_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.attendance_summaries_id_att_summary_seq OWNER TO ict;

--
-- TOC entry 5584 (class 0 OID 0)
-- Dependencies: 232
-- Name: attendance_summaries_id_att_summary_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.attendance_summaries_id_att_summary_seq OWNED BY public.attendance_summaries.id_att_summary;


--
-- TOC entry 233 (class 1259 OID 16521)
-- Name: cache; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache OWNER TO ict;

--
-- TOC entry 234 (class 1259 OID 16526)
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration integer NOT NULL
);


ALTER TABLE public.cache_locks OWNER TO ict;

--
-- TOC entry 235 (class 1259 OID 16531)
-- Name: cleareance_details; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.cleareance_details (
    id_cleareance_detail bigint NOT NULL,
    organisasi_id integer NOT NULL,
    cleareance_id character varying(255) NOT NULL,
    type character varying(255) NOT NULL,
    is_clear character varying(1) DEFAULT 'N'::character varying NOT NULL,
    keterangan text,
    confirmed_by_id character varying(255),
    confirmed_by character varying(255),
    confirmed_at timestamp(0) without time zone,
    attachment character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.cleareance_details OWNER TO ict;

--
-- TOC entry 236 (class 1259 OID 16537)
-- Name: cleareance_details_id_cleareance_detail_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.cleareance_details_id_cleareance_detail_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cleareance_details_id_cleareance_detail_seq OWNER TO ict;

--
-- TOC entry 5585 (class 0 OID 0)
-- Dependencies: 236
-- Name: cleareance_details_id_cleareance_detail_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.cleareance_details_id_cleareance_detail_seq OWNED BY public.cleareance_details.id_cleareance_detail;


--
-- TOC entry 237 (class 1259 OID 16538)
-- Name: cleareance_settings; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.cleareance_settings (
    id_cleareance_setting bigint NOT NULL,
    organisasi_id integer NOT NULL,
    type character varying(255) NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    ni_karyawan character varying(255) NOT NULL,
    nama_karyawan character varying(255) NOT NULL,
    signature character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.cleareance_settings OWNER TO ict;

--
-- TOC entry 238 (class 1259 OID 16543)
-- Name: cleareance_settings_id_cleareance_setting_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.cleareance_settings_id_cleareance_setting_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cleareance_settings_id_cleareance_setting_seq OWNER TO ict;

--
-- TOC entry 5586 (class 0 OID 0)
-- Dependencies: 238
-- Name: cleareance_settings_id_cleareance_setting_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.cleareance_settings_id_cleareance_setting_seq OWNED BY public.cleareance_settings.id_cleareance_setting;


--
-- TOC entry 239 (class 1259 OID 16544)
-- Name: cleareances; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.cleareances (
    id_cleareance character varying(255) NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    divisi_id integer,
    departemen_id integer,
    jabatan_id integer,
    posisi_id integer,
    nama_divisi character varying(255),
    nama_departemen character varying(255),
    nama_jabatan character varying(255),
    nama_posisi character varying(255),
    tanggal_akhir_bekerja date,
    status character varying(1) DEFAULT 'N'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.cleareances OWNER TO ict;

--
-- TOC entry 240 (class 1259 OID 16550)
-- Name: cutis; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.cutis (
    id_cuti integer NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer,
    penggunaan_sisa_cuti character varying(2) DEFAULT 'TB'::character varying NOT NULL,
    jenis_cuti character varying(255),
    jenis_cuti_id integer,
    durasi_cuti integer DEFAULT 1 NOT NULL,
    rencana_mulai_cuti date NOT NULL,
    rencana_selesai_cuti date NOT NULL,
    aktual_mulai_cuti date,
    aktual_selesai_cuti date,
    alasan_cuti text,
    karyawan_pengganti_id character varying(255),
    checked1_at timestamp(0) without time zone,
    checked1_by character varying(255),
    checked2_at timestamp(0) without time zone,
    checked2_by character varying(255),
    approved_at timestamp(0) without time zone,
    approved_by character varying(255),
    legalized_at timestamp(0) without time zone,
    legalized_by character varying(255),
    rejected_at timestamp(0) without time zone,
    rejected_by character varying(255),
    rejected_note text,
    status_dokumen character varying(255) DEFAULT 'WAITING'::character varying NOT NULL,
    status_cuti character varying(255),
    attachment character varying(255),
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT cutis_status_cuti_check CHECK (((status_cuti)::text = ANY (ARRAY[('SCHEDULED'::character varying)::text, ('ON LEAVE'::character varying)::text, ('COMPLETED'::character varying)::text, ('CANCELED'::character varying)::text]))),
    CONSTRAINT cutis_status_dokumen_check CHECK (((status_dokumen)::text = ANY (ARRAY[('WAITING'::character varying)::text, ('APPROVED'::character varying)::text, ('REJECTED'::character varying)::text])))
);


ALTER TABLE public.cutis OWNER TO ict;

--
-- TOC entry 241 (class 1259 OID 16560)
-- Name: cutis_id_cuti_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.cutis_id_cuti_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.cutis_id_cuti_seq OWNER TO ict;

--
-- TOC entry 5587 (class 0 OID 0)
-- Dependencies: 241
-- Name: cutis_id_cuti_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.cutis_id_cuti_seq OWNED BY public.cutis.id_cuti;


--
-- TOC entry 242 (class 1259 OID 16561)
-- Name: departemens; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.departemens (
    id_departemen integer NOT NULL,
    divisi_id integer NOT NULL,
    nama character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.departemens OWNER TO ict;

--
-- TOC entry 243 (class 1259 OID 16564)
-- Name: departemens_id_departemen_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.departemens_id_departemen_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.departemens_id_departemen_seq OWNER TO ict;

--
-- TOC entry 5588 (class 0 OID 0)
-- Dependencies: 243
-- Name: departemens_id_departemen_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.departemens_id_departemen_seq OWNED BY public.departemens.id_departemen;


--
-- TOC entry 244 (class 1259 OID 16565)
-- Name: detail_lemburs; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.detail_lemburs (
    id_detail_lembur integer NOT NULL,
    lembur_id character varying(255) NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    divisi_id integer,
    rencana_mulai_lembur timestamp(0) without time zone NOT NULL,
    rencana_selesai_lembur timestamp(0) without time zone NOT NULL,
    is_rencana_approved character varying(255) DEFAULT 'Y'::character varying NOT NULL,
    aktual_mulai_lembur timestamp(0) without time zone,
    aktual_selesai_lembur timestamp(0) without time zone,
    is_aktual_approved character varying(255) DEFAULT 'Y'::character varying NOT NULL,
    durasi integer DEFAULT 0 NOT NULL,
    deskripsi_pekerjaan text NOT NULL,
    keterangan text,
    nominal integer DEFAULT 0 NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    durasi_istirahat integer DEFAULT 0 NOT NULL,
    durasi_konversi_lembur integer DEFAULT 0 NOT NULL,
    gaji_lembur integer DEFAULT 0 NOT NULL,
    uang_makan integer DEFAULT 0 NOT NULL,
    pembagi_upah_lembur integer DEFAULT 173 NOT NULL,
    rencana_last_changed_by character varying(255),
    rencana_last_changed_at timestamp(0) without time zone,
    aktual_last_changed_by character varying(255),
    aktual_last_changed_at timestamp(0) without time zone,
    CONSTRAINT "detail_lemburs_is_aktual_approved _check" CHECK (((is_aktual_approved)::text = ANY (ARRAY[('Y'::character varying)::text, ('N'::character varying)::text]))),
    CONSTRAINT "detail_lemburs_is_rencana_approved _check" CHECK (((is_rencana_approved)::text = ANY (ARRAY[('Y'::character varying)::text, ('N'::character varying)::text])))
);


ALTER TABLE public.detail_lemburs OWNER TO ict;

--
-- TOC entry 245 (class 1259 OID 16581)
-- Name: detail_lemburs_id_detail_lembur_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.detail_lemburs_id_detail_lembur_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.detail_lemburs_id_detail_lembur_seq OWNER TO ict;

--
-- TOC entry 5589 (class 0 OID 0)
-- Dependencies: 245
-- Name: detail_lemburs_id_detail_lembur_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.detail_lemburs_id_detail_lembur_seq OWNED BY public.detail_lemburs.id_detail_lembur;


--
-- TOC entry 246 (class 1259 OID 16582)
-- Name: detail_millages; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.detail_millages (
    id_detail_millage bigint NOT NULL,
    organisasi_id integer NOT NULL,
    millage_id character varying(255) NOT NULL,
    type character varying(255) NOT NULL,
    attachment character varying(255) NOT NULL,
    nominal integer DEFAULT 0 NOT NULL,
    is_active character varying(255) DEFAULT 'Y'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.detail_millages OWNER TO ict;

--
-- TOC entry 247 (class 1259 OID 16589)
-- Name: detail_millages_id_detail_millage_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.detail_millages_id_detail_millage_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.detail_millages_id_detail_millage_seq OWNER TO ict;

--
-- TOC entry 5590 (class 0 OID 0)
-- Dependencies: 247
-- Name: detail_millages_id_detail_millage_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.detail_millages_id_detail_millage_seq OWNED BY public.detail_millages.id_detail_millage;


--
-- TOC entry 248 (class 1259 OID 16590)
-- Name: detail_tugasluars; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.detail_tugasluars (
    id_detail_tugasluar integer NOT NULL,
    tugasluar_id character varying(255) NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    divisi_id integer,
    ni_karyawan character varying(255),
    pin character varying(255),
    date date DEFAULT '2025-05-26'::date NOT NULL,
    is_active character varying(255) DEFAULT 'Y'::character varying NOT NULL,
    role character varying(1) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.detail_tugasluars OWNER TO ict;

--
-- TOC entry 249 (class 1259 OID 16597)
-- Name: detail_tugasluars_id_detail_tugasluar_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.detail_tugasluars_id_detail_tugasluar_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.detail_tugasluars_id_detail_tugasluar_seq OWNER TO ict;

--
-- TOC entry 5591 (class 0 OID 0)
-- Dependencies: 249
-- Name: detail_tugasluars_id_detail_tugasluar_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.detail_tugasluars_id_detail_tugasluar_seq OWNED BY public.detail_tugasluars.id_detail_tugasluar;


--
-- TOC entry 250 (class 1259 OID 16598)
-- Name: divisis; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.divisis (
    id_divisi integer NOT NULL,
    nama character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.divisis OWNER TO ict;

--
-- TOC entry 251 (class 1259 OID 16601)
-- Name: divisis_id_divisi_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.divisis_id_divisi_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.divisis_id_divisi_seq OWNER TO ict;

--
-- TOC entry 5592 (class 0 OID 0)
-- Dependencies: 251
-- Name: divisis_id_divisi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.divisis_id_divisi_seq OWNED BY public.divisis.id_divisi;


--
-- TOC entry 252 (class 1259 OID 16602)
-- Name: events; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.events (
    id_event integer NOT NULL,
    organisasi_id integer,
    jenis_event character varying(2) NOT NULL,
    keterangan character varying(255) NOT NULL,
    durasi integer NOT NULL,
    tanggal_mulai date NOT NULL,
    tanggal_selesai date NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.events OWNER TO ict;

--
-- TOC entry 253 (class 1259 OID 16605)
-- Name: events_id_event_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.events_id_event_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.events_id_event_seq OWNER TO ict;

--
-- TOC entry 5593 (class 0 OID 0)
-- Dependencies: 253
-- Name: events_id_event_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.events_id_event_seq OWNED BY public.events.id_event;


--
-- TOC entry 254 (class 1259 OID 16606)
-- Name: export_slip_lemburs; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.export_slip_lemburs (
    id_export_slip_lembur integer NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    periode date NOT NULL,
    status character varying(2) DEFAULT 'IP'::character varying NOT NULL,
    attachment character varying(255),
    message character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.export_slip_lemburs OWNER TO ict;

--
-- TOC entry 255 (class 1259 OID 16612)
-- Name: export_slip_lemburs_id_export_slip_lembur_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.export_slip_lemburs_id_export_slip_lembur_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.export_slip_lemburs_id_export_slip_lembur_seq OWNER TO ict;

--
-- TOC entry 5594 (class 0 OID 0)
-- Dependencies: 255
-- Name: export_slip_lemburs_id_export_slip_lembur_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.export_slip_lemburs_id_export_slip_lembur_seq OWNED BY public.export_slip_lemburs.id_export_slip_lembur;


--
-- TOC entry 256 (class 1259 OID 16613)
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection text NOT NULL,
    queue text NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


ALTER TABLE public.failed_jobs OWNER TO ict;

--
-- TOC entry 257 (class 1259 OID 16619)
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.failed_jobs_id_seq OWNER TO ict;

--
-- TOC entry 5595 (class 0 OID 0)
-- Dependencies: 257
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- TOC entry 258 (class 1259 OID 16620)
-- Name: gaji_departemens; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.gaji_departemens (
    id_gaji_departemen integer NOT NULL,
    departemen_id integer,
    periode date NOT NULL,
    total_gaji integer DEFAULT 0 NOT NULL,
    nominal_batas_lembur integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organisasi_id integer NOT NULL,
    presentase integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.gaji_departemens OWNER TO ict;

--
-- TOC entry 259 (class 1259 OID 16626)
-- Name: gaji_departemens_id_gaji_departemen_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.gaji_departemens_id_gaji_departemen_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.gaji_departemens_id_gaji_departemen_seq OWNER TO ict;

--
-- TOC entry 5596 (class 0 OID 0)
-- Dependencies: 259
-- Name: gaji_departemens_id_gaji_departemen_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.gaji_departemens_id_gaji_departemen_seq OWNED BY public.gaji_departemens.id_gaji_departemen;


--
-- TOC entry 260 (class 1259 OID 16627)
-- Name: grup_patterns; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.grup_patterns (
    id_grup_pattern integer NOT NULL,
    organisasi_id integer NOT NULL,
    nama character varying(255) NOT NULL,
    urutan json NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.grup_patterns OWNER TO ict;

--
-- TOC entry 261 (class 1259 OID 16632)
-- Name: grup_patterns_id_grup_pattern_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.grup_patterns_id_grup_pattern_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.grup_patterns_id_grup_pattern_seq OWNER TO ict;

--
-- TOC entry 5597 (class 0 OID 0)
-- Dependencies: 261
-- Name: grup_patterns_id_grup_pattern_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.grup_patterns_id_grup_pattern_seq OWNED BY public.grup_patterns.id_grup_pattern;


--
-- TOC entry 262 (class 1259 OID 16633)
-- Name: grups; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.grups (
    id_grup integer NOT NULL,
    nama character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    toleransi_waktu time(0) without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    jam_masuk time(0) without time zone DEFAULT '00:00:00'::time without time zone NOT NULL,
    jam_keluar time(0) without time zone DEFAULT '00:00:00'::time without time zone NOT NULL
);


ALTER TABLE public.grups OWNER TO ict;

--
-- TOC entry 263 (class 1259 OID 16639)
-- Name: grups_id_grup_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.grups_id_grup_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.grups_id_grup_seq OWNER TO ict;

--
-- TOC entry 5598 (class 0 OID 0)
-- Dependencies: 263
-- Name: grups_id_grup_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.grups_id_grup_seq OWNED BY public.grups.id_grup;


--
-- TOC entry 264 (class 1259 OID 16640)
-- Name: izins; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.izins (
    id_izin character varying(255) NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    divisi_id integer,
    rencana_mulai_or_masuk timestamp(0) without time zone,
    rencana_selesai_or_keluar timestamp(0) without time zone,
    aktual_mulai_or_masuk timestamp(0) without time zone,
    aktual_selesai_or_keluar timestamp(0) without time zone,
    durasi integer DEFAULT 0 NOT NULL,
    keterangan text,
    karyawan_pengganti_id character varying(255),
    checked_at timestamp(0) without time zone,
    checked_by character varying(255),
    approved_at timestamp(0) without time zone,
    approved_by character varying(255),
    legalized_at timestamp(0) without time zone,
    legalized_by character varying(255),
    rejected_at timestamp(0) without time zone,
    rejected_by character varying(255),
    rejected_note text,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    jenis_izin character varying(255) NOT NULL,
    CONSTRAINT izins_jenis_izin_check CHECK (((jenis_izin)::text = ANY (ARRAY[('TM'::character varying)::text, ('SH'::character varying)::text, ('KP'::character varying)::text, ('PL'::character varying)::text])))
);


ALTER TABLE public.izins OWNER TO ict;

--
-- TOC entry 265 (class 1259 OID 16647)
-- Name: jabatans; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.jabatans (
    id_jabatan integer NOT NULL,
    nama character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.jabatans OWNER TO ict;

--
-- TOC entry 266 (class 1259 OID 16650)
-- Name: jabatans_id_jabatan_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.jabatans_id_jabatan_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jabatans_id_jabatan_seq OWNER TO ict;

--
-- TOC entry 5599 (class 0 OID 0)
-- Dependencies: 266
-- Name: jabatans_id_jabatan_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.jabatans_id_jabatan_seq OWNED BY public.jabatans.id_jabatan;


--
-- TOC entry 267 (class 1259 OID 16651)
-- Name: jenis_cutis; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.jenis_cutis (
    id_jenis_cuti integer NOT NULL,
    jenis character varying(255) NOT NULL,
    durasi integer NOT NULL,
    "isUrgent" character varying(255) DEFAULT 'N'::character varying NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    "isWorkday" character varying(255) DEFAULT 'N'::character varying NOT NULL,
    CONSTRAINT "jenis_cutis_isUrgent_check" CHECK ((("isUrgent")::text = ANY (ARRAY[('Y'::character varying)::text, ('N'::character varying)::text]))),
    CONSTRAINT "jenis_cutis_isWorkday_check" CHECK ((("isWorkday")::text = ANY (ARRAY[('Y'::character varying)::text, ('N'::character varying)::text])))
);


ALTER TABLE public.jenis_cutis OWNER TO ict;

--
-- TOC entry 268 (class 1259 OID 16660)
-- Name: jenis_cutis_id_jenis_cuti_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.jenis_cutis_id_jenis_cuti_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jenis_cutis_id_jenis_cuti_seq OWNER TO ict;

--
-- TOC entry 5600 (class 0 OID 0)
-- Dependencies: 268
-- Name: jenis_cutis_id_jenis_cuti_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.jenis_cutis_id_jenis_cuti_seq OWNED BY public.jenis_cutis.id_jenis_cuti;


--
-- TOC entry 269 (class 1259 OID 16661)
-- Name: job_batches; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.job_batches (
    id character varying(255) NOT NULL,
    name character varying(255) NOT NULL,
    total_jobs integer NOT NULL,
    pending_jobs integer NOT NULL,
    failed_jobs integer NOT NULL,
    failed_job_ids text NOT NULL,
    options text,
    cancelled_at integer,
    created_at integer NOT NULL,
    finished_at integer
);


ALTER TABLE public.job_batches OWNER TO ict;

--
-- TOC entry 270 (class 1259 OID 16666)
-- Name: jobs; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.jobs (
    id bigint NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    attempts smallint NOT NULL,
    reserved_at integer,
    available_at integer NOT NULL,
    created_at integer NOT NULL
);


ALTER TABLE public.jobs OWNER TO ict;

--
-- TOC entry 271 (class 1259 OID 16671)
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.jobs_id_seq OWNER TO ict;

--
-- TOC entry 5601 (class 0 OID 0)
-- Dependencies: 271
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- TOC entry 272 (class 1259 OID 16672)
-- Name: karyawan_posisi; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.karyawan_posisi (
    id bigint NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    posisi_id integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.karyawan_posisi OWNER TO ict;

--
-- TOC entry 273 (class 1259 OID 16675)
-- Name: karyawan_posisi_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.karyawan_posisi_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.karyawan_posisi_id_seq OWNER TO ict;

--
-- TOC entry 5602 (class 0 OID 0)
-- Dependencies: 273
-- Name: karyawan_posisi_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.karyawan_posisi_id_seq OWNED BY public.karyawan_posisi.id;


--
-- TOC entry 274 (class 1259 OID 16676)
-- Name: karyawans; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.karyawans (
    id_karyawan character varying(255) NOT NULL,
    ni_karyawan character varying(255),
    user_id bigint,
    grup_id bigint,
    organisasi_id bigint,
    no_kk character varying(255),
    nik character varying(255),
    nama character varying(255),
    tempat_lahir character varying(255),
    tanggal_lahir date,
    alamat text,
    domisili text,
    email character varying(255),
    no_telp character varying(255),
    gol_darah character varying(255),
    jenis_kelamin character varying(255),
    agama character varying(255),
    status_keluarga character varying(255),
    kategori_keluarga character varying(255),
    npwp character varying(255),
    no_bpjs_kt character varying(255),
    no_bpjs_ks character varying(255),
    jenis_kontrak character varying(255),
    status_karyawan character varying(255),
    sisa_cuti_pribadi integer DEFAULT 0 NOT NULL,
    sisa_cuti_bersama integer DEFAULT 0 NOT NULL,
    sisa_cuti_tahun_lalu integer DEFAULT 0 NOT NULL,
    expired_date_cuti_tahun_lalu date,
    hutang_cuti integer DEFAULT 0 NOT NULL,
    no_rekening character varying(255),
    nama_rekening character varying(255),
    nama_bank character varying(255),
    nama_ibu_kandung character varying(255),
    jenjang_pendidikan character varying(255),
    jurusan_pendidikan character varying(255),
    no_telp_darurat character varying(255),
    tanggal_mulai date,
    tanggal_selesai date,
    foto character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    pin character varying(255),
    grup_pattern_id integer,
    CONSTRAINT karyawans_agama_check CHECK (((agama)::text = ANY (ARRAY[('ISLAM'::character varying)::text, ('KATOLIK'::character varying)::text, ('KRISTEN'::character varying)::text, ('KONGHUCU'::character varying)::text, ('HINDU'::character varying)::text, ('BUDHA'::character varying)::text, ('PROTESTAN'::character varying)::text, ('LAINNYA'::character varying)::text]))),
    CONSTRAINT karyawans_gol_darah_check CHECK (((gol_darah)::text = ANY (ARRAY[('A'::character varying)::text, ('B'::character varying)::text, ('AB'::character varying)::text, ('O'::character varying)::text]))),
    CONSTRAINT karyawans_jenis_kelamin_check CHECK (((jenis_kelamin)::text = ANY (ARRAY[('L'::character varying)::text, ('P'::character varying)::text]))),
    CONSTRAINT karyawans_jenis_kontrak_check CHECK (((jenis_kontrak)::text = ANY (ARRAY[('PKWT'::character varying)::text, ('MAGANG'::character varying)::text, ('PKWTT'::character varying)::text]))),
    CONSTRAINT karyawans_jenjang_pendidikan_check CHECK (((jenjang_pendidikan)::text = ANY (ARRAY[('SD'::character varying)::text, ('SMP'::character varying)::text, ('SMA'::character varying)::text, ('D1'::character varying)::text, ('D2'::character varying)::text, ('D3'::character varying)::text, ('D4'::character varying)::text, ('S1'::character varying)::text, ('S2'::character varying)::text, ('S3'::character varying)::text]))),
    CONSTRAINT karyawans_kategori_keluarga_check CHECK (((kategori_keluarga)::text = ANY (ARRAY[('TK0'::character varying)::text, ('TK1'::character varying)::text, ('TK2'::character varying)::text, ('TK3'::character varying)::text, ('K0'::character varying)::text, ('K1'::character varying)::text, ('K2'::character varying)::text, ('K3'::character varying)::text]))),
    CONSTRAINT karyawans_status_karyawan_check CHECK (((status_karyawan)::text = ANY (ARRAY[('AT'::character varying)::text, ('MD'::character varying)::text, ('PS'::character varying)::text, ('HK'::character varying)::text, ('TM'::character varying)::text]))),
    CONSTRAINT karyawans_status_keluarga_check CHECK (((status_keluarga)::text = ANY (ARRAY[('MENIKAH'::character varying)::text, ('BELUM MENIKAH'::character varying)::text, ('CERAI'::character varying)::text])))
);


ALTER TABLE public.karyawans OWNER TO ict;

--
-- TOC entry 275 (class 1259 OID 16693)
-- Name: kontraks; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.kontraks (
    id_kontrak character varying(255) NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    posisi_id integer,
    organisasi_id integer,
    nama_posisi character varying(255),
    no_surat character varying(255),
    jenis character varying(255) NOT NULL,
    status character varying(255) DEFAULT 'ON PROGRESS'::character varying NOT NULL,
    durasi integer,
    salary integer,
    deskripsi text,
    tanggal_mulai date NOT NULL,
    tanggal_selesai date,
    tanggal_mulai_before date,
    tanggal_selesai_before date,
    "isReactive" character varying(255) DEFAULT 'N'::character varying NOT NULL,
    issued_date date,
    tempat_administrasi character varying(255) DEFAULT 'Karawang'::character varying,
    status_change_by character varying(255),
    status_change_date date,
    attachment character varying(255),
    evidence character varying(255),
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT "kontraks_isReactive_check" CHECK ((("isReactive")::text = ANY (ARRAY[('Y'::character varying)::text, ('N'::character varying)::text]))),
    CONSTRAINT kontraks_jenis_check CHECK (((jenis)::text = ANY (ARRAY[('PKWT'::character varying)::text, ('MAGANG'::character varying)::text, ('PKWTT'::character varying)::text]))),
    CONSTRAINT kontraks_status_check CHECK (((status)::text = ANY (ARRAY[('DONE'::character varying)::text, ('ON PROGRESS'::character varying)::text])))
);


ALTER TABLE public.kontraks OWNER TO ict;

--
-- TOC entry 276 (class 1259 OID 16704)
-- Name: ksk; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.ksk (
    id_ksk character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    divisi_id integer,
    nama_divisi character varying(255),
    departemen_id integer,
    nama_departemen character varying(255),
    release_date date DEFAULT '2025-05-26'::date NOT NULL,
    parent_id integer,
    released_by_id character varying(255),
    released_by character varying(255),
    released_at timestamp(0) without time zone,
    checked_by_id character varying(255),
    checked_by character varying(255),
    checked_at timestamp(0) without time zone,
    approved_by_id character varying(255),
    approved_by character varying(255),
    approved_at timestamp(0) without time zone,
    reviewed_div_by_id character varying(255),
    reviewed_div_by character varying(255),
    reviewed_div_at timestamp(0) without time zone,
    reviewed_ph_by_id character varying(255),
    reviewed_ph_by character varying(255),
    reviewed_ph_at timestamp(0) without time zone,
    reviewed_dir_by_id character varying(255),
    reviewed_dir_by character varying(255),
    reviewed_dir_at timestamp(0) without time zone,
    legalized_by character varying(255),
    legalized_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.ksk OWNER TO ict;

--
-- TOC entry 277 (class 1259 OID 16710)
-- Name: ksk_change_histories; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.ksk_change_histories (
    id_ksk_change_history bigint NOT NULL,
    ksk_detail_id integer NOT NULL,
    changed_by_id character varying(255) NOT NULL,
    changed_by character varying(255) NOT NULL,
    changed_at timestamp(0) without time zone NOT NULL,
    reason text,
    status_ksk_before character varying(255) NOT NULL,
    status_ksk_after character varying(255) NOT NULL,
    durasi_before integer NOT NULL,
    durasi_after integer NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    jenis_kontrak_before character varying(255),
    jenis_kontrak_after character varying(255)
);


ALTER TABLE public.ksk_change_histories OWNER TO ict;

--
-- TOC entry 278 (class 1259 OID 16715)
-- Name: ksk_change_histories_id_ksk_change_history_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.ksk_change_histories_id_ksk_change_history_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ksk_change_histories_id_ksk_change_history_seq OWNER TO ict;

--
-- TOC entry 5603 (class 0 OID 0)
-- Dependencies: 278
-- Name: ksk_change_histories_id_ksk_change_history_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.ksk_change_histories_id_ksk_change_history_seq OWNED BY public.ksk_change_histories.id_ksk_change_history;


--
-- TOC entry 279 (class 1259 OID 16716)
-- Name: ksk_details; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.ksk_details (
    id_ksk_detail integer NOT NULL,
    ksk_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    divisi_id integer,
    nama_divisi character varying(255),
    departemen_id integer,
    nama_departemen character varying(255),
    karyawan_id character varying(255) NOT NULL,
    ni_karyawan character varying(255) NOT NULL,
    nama_karyawan character varying(255) NOT NULL,
    posisi_id integer NOT NULL,
    nama_posisi character varying(255) NOT NULL,
    jabatan_id integer NOT NULL,
    nama_jabatan character varying(255) NOT NULL,
    jenis_kontrak character varying(255) NOT NULL,
    jumlah_surat_peringatan integer DEFAULT 0 NOT NULL,
    jumlah_sakit integer DEFAULT 0 NOT NULL,
    jumlah_izin integer DEFAULT 0 NOT NULL,
    jumlah_alpa integer DEFAULT 0 NOT NULL,
    status_ksk character varying(255),
    tanggal_renewal_kontrak date,
    durasi_renewal integer DEFAULT 0 NOT NULL,
    cleareance_id character varying(255),
    kontrak_id character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.ksk_details OWNER TO ict;

--
-- TOC entry 280 (class 1259 OID 16726)
-- Name: ksk_details_id_ksk_detail_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.ksk_details_id_ksk_detail_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.ksk_details_id_ksk_detail_seq OWNER TO ict;

--
-- TOC entry 5604 (class 0 OID 0)
-- Dependencies: 280
-- Name: ksk_details_id_ksk_detail_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.ksk_details_id_ksk_detail_seq OWNED BY public.ksk_details.id_ksk_detail;


--
-- TOC entry 281 (class 1259 OID 16727)
-- Name: lembur_harians; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.lembur_harians (
    id_lembur_harian integer NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    divisi_id integer,
    total_durasi_lembur integer DEFAULT 0 NOT NULL,
    total_nominal_lembur integer DEFAULT 0 NOT NULL,
    tanggal_lembur date NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.lembur_harians OWNER TO ict;

--
-- TOC entry 282 (class 1259 OID 16732)
-- Name: lembur_harians_id_lembur_harian_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.lembur_harians_id_lembur_harian_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.lembur_harians_id_lembur_harian_seq OWNER TO ict;

--
-- TOC entry 5605 (class 0 OID 0)
-- Dependencies: 282
-- Name: lembur_harians_id_lembur_harian_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.lembur_harians_id_lembur_harian_seq OWNED BY public.lembur_harians.id_lembur_harian;


--
-- TOC entry 283 (class 1259 OID 16733)
-- Name: lemburs; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.lemburs (
    id_lembur character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    divisi_id integer,
    plan_checked_by character varying(255),
    plan_checked_at timestamp(0) without time zone,
    plan_approved_by character varying(255),
    plan_approved_at timestamp(0) without time zone,
    plan_legalized_by character varying(255),
    plan_legalized_at timestamp(0) without time zone,
    actual_checked_by character varying(255),
    actual_checked_at timestamp(0) without time zone,
    actual_approved_by character varying(255),
    actual_approved_at timestamp(0) without time zone,
    actual_legalized_by character varying(255),
    actual_legalized_at timestamp(0) without time zone,
    total_durasi integer DEFAULT 0 NOT NULL,
    status character varying(255) DEFAULT 'WAITING'::character varying NOT NULL,
    attachment character varying(255),
    issued_date timestamp(0) without time zone DEFAULT '2025-05-26 10:07:51'::timestamp without time zone NOT NULL,
    issued_by character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    jenis_hari character varying(255) NOT NULL,
    rejected_by character varying(255),
    rejected_at timestamp(0) without time zone,
    rejected_note text,
    plan_reviewed_by character varying(255),
    plan_reviewed_at timestamp(0) without time zone,
    actual_reviewed_by character varying(255),
    actual_reviewed_at timestamp(0) without time zone,
    total_nominal integer DEFAULT 0 NOT NULL,
    CONSTRAINT lemburs_jenis_hari_check CHECK (((jenis_hari)::text = ANY (ARRAY[('WD'::character varying)::text, ('WE'::character varying)::text]))),
    CONSTRAINT lemburs_status_check CHECK (((status)::text = ANY (ARRAY[('WAITING'::character varying)::text, ('PLANNED'::character varying)::text, ('COMPLETED'::character varying)::text, ('REJECTED'::character varying)::text])))
);


ALTER TABLE public.lemburs OWNER TO ict;

--
-- TOC entry 284 (class 1259 OID 16744)
-- Name: migrations; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


ALTER TABLE public.migrations OWNER TO ict;

--
-- TOC entry 285 (class 1259 OID 16747)
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.migrations_id_seq OWNER TO ict;

--
-- TOC entry 5606 (class 0 OID 0)
-- Dependencies: 285
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- TOC entry 286 (class 1259 OID 16748)
-- Name: millages; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.millages (
    id_millage character varying(255) NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    divisi_id integer,
    nama_karyawan character varying(255) NOT NULL,
    ni_karyawan character varying(255) NOT NULL,
    no_polisi character varying(255) NOT NULL,
    is_claimed character varying(255) DEFAULT 'N'::character varying NOT NULL,
    checked_by character varying(255),
    checked_at timestamp(0) without time zone,
    legalized_by character varying(255),
    legalized_at timestamp(0) without time zone,
    rejected_by character varying(255),
    rejected_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT millages_is_claimed_check CHECK (((is_claimed)::text = ANY (ARRAY[('Y'::character varying)::text, ('N'::character varying)::text])))
);


ALTER TABLE public.millages OWNER TO ict;

--
-- TOC entry 287 (class 1259 OID 16755)
-- Name: model_has_permissions; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.model_has_permissions (
    permission_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_permissions OWNER TO ict;

--
-- TOC entry 288 (class 1259 OID 16758)
-- Name: model_has_roles; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.model_has_roles (
    role_id bigint NOT NULL,
    model_type character varying(255) NOT NULL,
    model_id bigint NOT NULL
);


ALTER TABLE public.model_has_roles OWNER TO ict;

--
-- TOC entry 289 (class 1259 OID 16761)
-- Name: organisasis; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.organisasis (
    id_organisasi bigint NOT NULL,
    nama character varying(255) NOT NULL,
    alamat character varying(255),
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.organisasis OWNER TO ict;

--
-- TOC entry 290 (class 1259 OID 16766)
-- Name: organisasis_id_organisasi_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.organisasis_id_organisasi_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.organisasis_id_organisasi_seq OWNER TO ict;

--
-- TOC entry 5607 (class 0 OID 0)
-- Dependencies: 290
-- Name: organisasis_id_organisasi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.organisasis_id_organisasi_seq OWNED BY public.organisasis.id_organisasi;


--
-- TOC entry 291 (class 1259 OID 16767)
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


ALTER TABLE public.password_reset_tokens OWNER TO ict;

--
-- TOC entry 292 (class 1259 OID 16772)
-- Name: permissions; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.permissions (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.permissions OWNER TO ict;

--
-- TOC entry 293 (class 1259 OID 16777)
-- Name: permissions_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.permissions_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.permissions_id_seq OWNER TO ict;

--
-- TOC entry 5608 (class 0 OID 0)
-- Dependencies: 293
-- Name: permissions_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.permissions_id_seq OWNED BY public.permissions.id;


--
-- TOC entry 294 (class 1259 OID 16778)
-- Name: personal_access_tokens; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.personal_access_tokens (
    id bigint NOT NULL,
    tokenable_type character varying(255) NOT NULL,
    tokenable_id bigint NOT NULL,
    name character varying(255) NOT NULL,
    token character varying(64) NOT NULL,
    abilities text,
    last_used_at timestamp(0) without time zone,
    expires_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.personal_access_tokens OWNER TO ict;

--
-- TOC entry 295 (class 1259 OID 16783)
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.personal_access_tokens_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.personal_access_tokens_id_seq OWNER TO ict;

--
-- TOC entry 5609 (class 0 OID 0)
-- Dependencies: 295
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.personal_access_tokens_id_seq OWNED BY public.personal_access_tokens.id;


--
-- TOC entry 296 (class 1259 OID 16784)
-- Name: pikets; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.pikets (
    id_piket integer NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    expired_date date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.pikets OWNER TO ict;

--
-- TOC entry 297 (class 1259 OID 16787)
-- Name: pikets_id_piket_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.pikets_id_piket_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.pikets_id_piket_seq OWNER TO ict;

--
-- TOC entry 5610 (class 0 OID 0)
-- Dependencies: 297
-- Name: pikets_id_piket_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.pikets_id_piket_seq OWNED BY public.pikets.id_piket;


--
-- TOC entry 298 (class 1259 OID 16788)
-- Name: posisis; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.posisis (
    id_posisi integer NOT NULL,
    jabatan_id integer NOT NULL,
    organisasi_id integer,
    divisi_id integer,
    departemen_id integer,
    seksi_id integer,
    nama character varying(255) NOT NULL,
    parent_id integer NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.posisis OWNER TO ict;

--
-- TOC entry 299 (class 1259 OID 16791)
-- Name: posisis_id_posisi_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.posisis_id_posisi_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.posisis_id_posisi_seq OWNER TO ict;

--
-- TOC entry 5611 (class 0 OID 0)
-- Dependencies: 299
-- Name: posisis_id_posisi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.posisis_id_posisi_seq OWNED BY public.posisis.id_posisi;


--
-- TOC entry 332 (class 1259 OID 33892)
-- Name: rekap_lembur; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.rekap_lembur (
    id bigint NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id bigint NOT NULL,
    departemen character varying(255) NOT NULL,
    jabatan character varying(255) NOT NULL,
    periode character varying(255) NOT NULL,
    gaji_pokok bigint,
    upah_lembur_per_jam bigint,
    total_jam_lembur numeric(8,2),
    konversi_jam_lembur numeric(8,2),
    gaji_lembur bigint,
    uang_makan bigint,
    total_gaji_lembur bigint,
    is_locked boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    pph_persen numeric(5,2),
    total_pph bigint,
    total_diterima bigint
);


ALTER TABLE public.rekap_lembur OWNER TO ict;

--
-- TOC entry 331 (class 1259 OID 33891)
-- Name: rekap_lembur_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.rekap_lembur_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rekap_lembur_id_seq OWNER TO ict;

--
-- TOC entry 5612 (class 0 OID 0)
-- Dependencies: 331
-- Name: rekap_lembur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.rekap_lembur_id_seq OWNED BY public.rekap_lembur.id;


--
-- TOC entry 334 (class 1259 OID 33904)
-- Name: rekap_lembur_summary; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.rekap_lembur_summary (
    id bigint NOT NULL,
    organisasi_id bigint NOT NULL,
    departemen character varying(255) NOT NULL,
    periode character varying(255) NOT NULL,
    jumlah_karyawan integer,
    total_jam_lembur numeric(8,2),
    konversi_jam_lembur numeric(8,2),
    gaji_lembur bigint,
    uang_makan bigint,
    total_gaji_lembur bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    is_locked boolean DEFAULT false NOT NULL,
    pph_persen numeric(5,2),
    total_pph bigint,
    total_gaji_lembur_diterima bigint
);


ALTER TABLE public.rekap_lembur_summary OWNER TO ict;

--
-- TOC entry 333 (class 1259 OID 33903)
-- Name: rekap_lembur_summary_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.rekap_lembur_summary_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.rekap_lembur_summary_id_seq OWNER TO ict;

--
-- TOC entry 5613 (class 0 OID 0)
-- Dependencies: 333
-- Name: rekap_lembur_summary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.rekap_lembur_summary_id_seq OWNED BY public.rekap_lembur_summary.id;


--
-- TOC entry 300 (class 1259 OID 16792)
-- Name: reset_cutis; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.reset_cutis (
    id_reset_cuti integer NOT NULL,
    reset_at timestamp(0) without time zone NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    reset_count integer DEFAULT 0 NOT NULL
);


ALTER TABLE public.reset_cutis OWNER TO ict;

--
-- TOC entry 301 (class 1259 OID 16796)
-- Name: reset_cutis_id_reset_cuti_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.reset_cutis_id_reset_cuti_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.reset_cutis_id_reset_cuti_seq OWNER TO ict;

--
-- TOC entry 5614 (class 0 OID 0)
-- Dependencies: 301
-- Name: reset_cutis_id_reset_cuti_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.reset_cutis_id_reset_cuti_seq OWNED BY public.reset_cutis.id_reset_cuti;


--
-- TOC entry 302 (class 1259 OID 16797)
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO ict;

--
-- TOC entry 303 (class 1259 OID 16800)
-- Name: roles; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    guard_name character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.roles OWNER TO ict;

--
-- TOC entry 304 (class 1259 OID 16805)
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.roles_id_seq OWNER TO ict;

--
-- TOC entry 5615 (class 0 OID 0)
-- Dependencies: 304
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 305 (class 1259 OID 16806)
-- Name: sakits; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.sakits (
    id_sakit integer NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    departemen_id integer,
    divisi_id integer,
    tanggal_mulai date NOT NULL,
    tanggal_selesai date,
    durasi integer DEFAULT 0 NOT NULL,
    keterangan text,
    karyawan_pengganti_id character varying(255),
    approved_at timestamp(0) without time zone,
    approved_by character varying(255),
    legalized_at timestamp(0) without time zone,
    legalized_by character varying(255),
    rejected_at timestamp(0) without time zone,
    rejected_by character varying(255),
    rejected_note text,
    attachment character varying(255),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.sakits OWNER TO ict;

--
-- TOC entry 306 (class 1259 OID 16812)
-- Name: sakits_id_sakit_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.sakits_id_sakit_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sakits_id_sakit_seq OWNER TO ict;

--
-- TOC entry 5616 (class 0 OID 0)
-- Dependencies: 306
-- Name: sakits_id_sakit_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.sakits_id_sakit_seq OWNED BY public.sakits.id_sakit;


--
-- TOC entry 307 (class 1259 OID 16813)
-- Name: seksis; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.seksis (
    id_seksi integer NOT NULL,
    departemen_id integer NOT NULL,
    nama character varying(255) NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.seksis OWNER TO ict;

--
-- TOC entry 308 (class 1259 OID 16816)
-- Name: seksis_id_seksi_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.seksis_id_seksi_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.seksis_id_seksi_seq OWNER TO ict;

--
-- TOC entry 5617 (class 0 OID 0)
-- Dependencies: 308
-- Name: seksis_id_seksi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.seksis_id_seksi_seq OWNED BY public.seksis.id_seksi;


--
-- TOC entry 309 (class 1259 OID 16817)
-- Name: sessions; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


ALTER TABLE public.sessions OWNER TO ict;

--
-- TOC entry 310 (class 1259 OID 16822)
-- Name: setting_lembur_karyawans; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.setting_lembur_karyawans (
    id_setting_lembur_karyawan integer NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    jabatan_id integer NOT NULL,
    departemen_id integer,
    gaji integer DEFAULT 0 NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.setting_lembur_karyawans OWNER TO ict;

--
-- TOC entry 311 (class 1259 OID 16826)
-- Name: setting_lembur_karyawans_id_setting_lembur_karyawan_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.setting_lembur_karyawans_id_setting_lembur_karyawan_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.setting_lembur_karyawans_id_setting_lembur_karyawan_seq OWNER TO ict;

--
-- TOC entry 5618 (class 0 OID 0)
-- Dependencies: 311
-- Name: setting_lembur_karyawans_id_setting_lembur_karyawan_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.setting_lembur_karyawans_id_setting_lembur_karyawan_seq OWNED BY public.setting_lembur_karyawans.id_setting_lembur_karyawan;


--
-- TOC entry 312 (class 1259 OID 16827)
-- Name: setting_lemburs; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.setting_lemburs (
    id_setting_lembur integer NOT NULL,
    organisasi_id integer NOT NULL,
    setting_name character varying(255) NOT NULL,
    value character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.setting_lemburs OWNER TO ict;

--
-- TOC entry 313 (class 1259 OID 16832)
-- Name: setting_lemburs_id_setting_lembur_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.setting_lemburs_id_setting_lembur_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.setting_lemburs_id_setting_lembur_seq OWNER TO ict;

--
-- TOC entry 5619 (class 0 OID 0)
-- Dependencies: 313
-- Name: setting_lemburs_id_setting_lembur_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.setting_lemburs_id_setting_lembur_seq OWNED BY public.setting_lemburs.id_setting_lembur;


--
-- TOC entry 314 (class 1259 OID 16833)
-- Name: setting_tugasluars; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.setting_tugasluars (
    id_setting_tugasluar integer NOT NULL,
    organisasi_id integer NOT NULL,
    name character varying(255) NOT NULL,
    value character varying(255) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.setting_tugasluars OWNER TO ict;

--
-- TOC entry 315 (class 1259 OID 16838)
-- Name: setting_tugasluars_id_setting_tugasluar_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.setting_tugasluars_id_setting_tugasluar_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.setting_tugasluars_id_setting_tugasluar_seq OWNER TO ict;

--
-- TOC entry 5620 (class 0 OID 0)
-- Dependencies: 315
-- Name: setting_tugasluars_id_setting_tugasluar_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.setting_tugasluars_id_setting_tugasluar_seq OWNED BY public.setting_tugasluars.id_setting_tugasluar;


--
-- TOC entry 330 (class 1259 OID 33875)
-- Name: slip_lembur_karyawans; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.slip_lembur_karyawans (
    id bigint NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    periode character varying(7) NOT NULL,
    total_lembur numeric(18,2) DEFAULT '0'::numeric NOT NULL,
    total_uang_makan numeric(18,2) DEFAULT '0'::numeric NOT NULL,
    total_jam numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    total_konversi_jam numeric(8,2) DEFAULT '0'::numeric NOT NULL,
    pph_persen integer DEFAULT 0 NOT NULL,
    total_pph numeric(18,2) DEFAULT '0'::numeric NOT NULL,
    total_diterima numeric(18,2) DEFAULT '0'::numeric NOT NULL,
    is_locked boolean DEFAULT false NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.slip_lembur_karyawans OWNER TO ict;

--
-- TOC entry 329 (class 1259 OID 33874)
-- Name: slip_lembur_karyawans_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.slip_lembur_karyawans_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.slip_lembur_karyawans_id_seq OWNER TO ict;

--
-- TOC entry 5621 (class 0 OID 0)
-- Dependencies: 329
-- Name: slip_lembur_karyawans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.slip_lembur_karyawans_id_seq OWNED BY public.slip_lembur_karyawans.id;


--
-- TOC entry 316 (class 1259 OID 16839)
-- Name: sto_headers; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.sto_headers (
    id_sto_header integer NOT NULL,
    year character varying(255) NOT NULL,
    issued_by character varying(255) NOT NULL,
    issued_name character varying(255) NOT NULL,
    organization_id integer NOT NULL,
    wh_id integer NOT NULL,
    wh_name character varying(255) NOT NULL,
    doc_date date DEFAULT '2025-05-26'::date NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.sto_headers OWNER TO ict;

--
-- TOC entry 317 (class 1259 OID 16845)
-- Name: sto_headers_id_sto_header_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.sto_headers_id_sto_header_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sto_headers_id_sto_header_seq OWNER TO ict;

--
-- TOC entry 5622 (class 0 OID 0)
-- Dependencies: 317
-- Name: sto_headers_id_sto_header_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.sto_headers_id_sto_header_seq OWNED BY public.sto_headers.id_sto_header;


--
-- TOC entry 318 (class 1259 OID 16846)
-- Name: sto_lines; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.sto_lines (
    id_sto_line integer NOT NULL,
    inputed_by character varying(255),
    inputed_name character varying(255),
    updated_by character varying(255),
    updated_name character varying(255),
    sto_header_id integer NOT NULL,
    customer_id integer,
    customer_name character varying(255),
    location_area character varying(255),
    wh_id integer,
    wh_name character varying(255),
    locator_id integer,
    locator_value character varying(255),
    no_label character varying(255) NOT NULL,
    spec_size character varying(255),
    product_id integer,
    part_code character varying(255),
    part_name character varying(255),
    part_desc character varying(255),
    model character varying(255),
    identitas_lot character varying(255),
    quantity character varying(255),
    status character varying(255),
    processed character varying(1) DEFAULT 'N'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone
);


ALTER TABLE public.sto_lines OWNER TO ict;

--
-- TOC entry 319 (class 1259 OID 16852)
-- Name: sto_lines_id_sto_line_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.sto_lines_id_sto_line_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sto_lines_id_sto_line_seq OWNER TO ict;

--
-- TOC entry 5623 (class 0 OID 0)
-- Dependencies: 319
-- Name: sto_lines_id_sto_line_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.sto_lines_id_sto_line_seq OWNED BY public.sto_lines.id_sto_line;


--
-- TOC entry 320 (class 1259 OID 16853)
-- Name: sto_upload; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.sto_upload (
    id_sto_upload bigint NOT NULL,
    wh_id integer NOT NULL,
    wh_name character varying(255) NOT NULL,
    locator_id integer NOT NULL,
    locator_name character varying(255) NOT NULL,
    customer_id integer NOT NULL,
    customer_name character varying(255) NOT NULL,
    product_id integer NOT NULL,
    product_code character varying(255),
    product_name character varying(255),
    product_desc character varying(255),
    model character varying(255),
    qty_book character varying(255) DEFAULT '0'::character varying NOT NULL,
    qty_count character varying(255) DEFAULT '0'::character varying NOT NULL,
    balance character varying(255) NOT NULL,
    doc_date date DEFAULT '2025-05-26'::date NOT NULL,
    processed character varying(255) DEFAULT 'N'::character varying NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    organization_id integer
);


ALTER TABLE public.sto_upload OWNER TO ict;

--
-- TOC entry 321 (class 1259 OID 16862)
-- Name: sto_upload_id_sto_upload_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.sto_upload_id_sto_upload_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.sto_upload_id_sto_upload_seq OWNER TO ict;

--
-- TOC entry 5624 (class 0 OID 0)
-- Dependencies: 321
-- Name: sto_upload_id_sto_upload_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.sto_upload_id_sto_upload_seq OWNED BY public.sto_upload.id_sto_upload;


--
-- TOC entry 322 (class 1259 OID 16863)
-- Name: templates; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.templates (
    id_template integer NOT NULL,
    organisasi_id integer,
    nama character varying(255) NOT NULL,
    type character varying(255) NOT NULL,
    template_path character varying(255) NOT NULL,
    "isActive" character varying(255) DEFAULT 'N'::character varying NOT NULL,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    CONSTRAINT "templates_isActive_check" CHECK ((("isActive")::text = ANY (ARRAY[('Y'::character varying)::text, ('N'::character varying)::text])))
);


ALTER TABLE public.templates OWNER TO ict;

--
-- TOC entry 323 (class 1259 OID 16870)
-- Name: templates_id_template_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.templates_id_template_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.templates_id_template_seq OWNER TO ict;

--
-- TOC entry 5625 (class 0 OID 0)
-- Dependencies: 323
-- Name: templates_id_template_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.templates_id_template_seq OWNED BY public.templates.id_template;


--
-- TOC entry 324 (class 1259 OID 16871)
-- Name: tugasluars; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.tugasluars (
    id_tugasluar character varying(255) NOT NULL,
    organisasi_id integer NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    ni_karyawan character varying(255) NOT NULL,
    divisi_id integer,
    departemen_id integer,
    tanggal date DEFAULT '2025-05-26'::date NOT NULL,
    tanggal_pergi_planning timestamp(0) without time zone,
    tanggal_kembali_planning timestamp(0) without time zone,
    tanggal_pergi_aktual timestamp(0) without time zone,
    tanggal_kembali_aktual timestamp(0) without time zone,
    jenis_kendaraan character varying(255) NOT NULL,
    jenis_kepemilikan character varying(2) NOT NULL,
    jenis_keberangkatan character varying(3) NOT NULL,
    no_polisi character varying(255),
    km_awal integer DEFAULT 0 NOT NULL,
    km_akhir integer DEFAULT 0 NOT NULL,
    km_selisih integer DEFAULT 0 NOT NULL,
    km_standar integer DEFAULT 0 NOT NULL,
    pengemudi_id character varying(255) NOT NULL,
    tempat_asal character varying(255) NOT NULL,
    tempat_tujuan character varying(255) NOT NULL,
    keterangan text NOT NULL,
    pembagi double precision DEFAULT '1'::double precision NOT NULL,
    bbm double precision DEFAULT '0'::double precision NOT NULL,
    rate integer DEFAULT 0 NOT NULL,
    nominal integer DEFAULT 0 NOT NULL,
    millage_id character varying(255),
    status character varying(255) DEFAULT 'WAITING'::character varying NOT NULL,
    checked_by character varying(255),
    checked_at timestamp(0) without time zone,
    legalized_by character varying(255),
    legalized_at timestamp(0) without time zone,
    rejected_by character varying(255),
    rejected_at timestamp(0) without time zone,
    rejected_note text,
    last_changed_by character varying(255),
    last_changed_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.tugasluars OWNER TO ict;

--
-- TOC entry 325 (class 1259 OID 16886)
-- Name: turnovers; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.turnovers (
    id_turnover integer NOT NULL,
    karyawan_id character varying(255) NOT NULL,
    organisasi_id integer,
    status_karyawan character varying(255) NOT NULL,
    tanggal_keluar date,
    keterangan text,
    jumlah_aktif_karyawan_terakhir integer,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    deleted_at timestamp(0) without time zone,
    CONSTRAINT turnovers_status_karyawan_check CHECK (((status_karyawan)::text = ANY (ARRAY[('MD'::character varying)::text, ('PS'::character varying)::text, ('HK'::character varying)::text, ('TM'::character varying)::text])))
);


ALTER TABLE public.turnovers OWNER TO ict;

--
-- TOC entry 326 (class 1259 OID 16892)
-- Name: turnovers_id_turnover_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.turnovers_id_turnover_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.turnovers_id_turnover_seq OWNER TO ict;

--
-- TOC entry 5626 (class 0 OID 0)
-- Dependencies: 326
-- Name: turnovers_id_turnover_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.turnovers_id_turnover_seq OWNED BY public.turnovers.id_turnover;


--
-- TOC entry 327 (class 1259 OID 16893)
-- Name: users; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    username character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    organisasi_id integer,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


ALTER TABLE public.users OWNER TO ict;

--
-- TOC entry 328 (class 1259 OID 16898)
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: ict
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


ALTER SEQUENCE public.users_id_seq OWNER TO ict;

--
-- TOC entry 5627 (class 0 OID 0)
-- Dependencies: 328
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 4953 (class 2604 OID 16899)
-- Name: activity_log id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.activity_log ALTER COLUMN id SET DEFAULT nextval('public.activity_log_id_seq'::regclass);


--
-- TOC entry 4954 (class 2604 OID 16900)
-- Name: approval_cutis id_approval_cuti; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.approval_cutis ALTER COLUMN id_approval_cuti SET DEFAULT nextval('public.approval_cutis_id_approval_cuti_seq'::regclass);


--
-- TOC entry 4955 (class 2604 OID 16901)
-- Name: attachment_ksk_details id_attachment_ksk_detail; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_ksk_details ALTER COLUMN id_attachment_ksk_detail SET DEFAULT nextval('public.attachment_ksk_details_id_attachment_ksk_detail_seq'::regclass);


--
-- TOC entry 4956 (class 2604 OID 16902)
-- Name: attachment_lemburs id_attachment_lembur; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_lemburs ALTER COLUMN id_attachment_lembur SET DEFAULT nextval('public.attachment_lemburs_id_attachment_lembur_seq'::regclass);


--
-- TOC entry 4957 (class 2604 OID 16903)
-- Name: attendance_devices id_device; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices ALTER COLUMN id_device SET DEFAULT nextval('public.attendance_devices_id_device_seq'::regclass);


--
-- TOC entry 4958 (class 2604 OID 16904)
-- Name: attendance_gps id_att_gps; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_gps ALTER COLUMN id_att_gps SET DEFAULT nextval('public.attendance_gps_id_att_gps_seq'::regclass);


--
-- TOC entry 4959 (class 2604 OID 16905)
-- Name: attendance_karyawan_grup id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_karyawan_grup ALTER COLUMN id SET DEFAULT nextval('public.attendance_karyawan_grup_id_seq'::regclass);


--
-- TOC entry 4963 (class 2604 OID 16906)
-- Name: attendance_scanlogs id_scanlog; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_scanlogs ALTER COLUMN id_scanlog SET DEFAULT nextval('public.attendance_scanlogs_id_scanlog_seq'::regclass);


--
-- TOC entry 4964 (class 2604 OID 16907)
-- Name: attendance_summaries id_att_summary; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_summaries ALTER COLUMN id_att_summary SET DEFAULT nextval('public.attendance_summaries_id_att_summary_seq'::regclass);


--
-- TOC entry 5033 (class 2604 OID 16908)
-- Name: cleareance_details id_cleareance_detail; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_details ALTER COLUMN id_cleareance_detail SET DEFAULT nextval('public.cleareance_details_id_cleareance_detail_seq'::regclass);


--
-- TOC entry 5035 (class 2604 OID 16909)
-- Name: cleareance_settings id_cleareance_setting; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_settings ALTER COLUMN id_cleareance_setting SET DEFAULT nextval('public.cleareance_settings_id_cleareance_setting_seq'::regclass);


--
-- TOC entry 5037 (class 2604 OID 16910)
-- Name: cutis id_cuti; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cutis ALTER COLUMN id_cuti SET DEFAULT nextval('public.cutis_id_cuti_seq'::regclass);


--
-- TOC entry 5041 (class 2604 OID 16911)
-- Name: departemens id_departemen; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.departemens ALTER COLUMN id_departemen SET DEFAULT nextval('public.departemens_id_departemen_seq'::regclass);


--
-- TOC entry 5042 (class 2604 OID 16912)
-- Name: detail_lemburs id_detail_lembur; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs ALTER COLUMN id_detail_lembur SET DEFAULT nextval('public.detail_lemburs_id_detail_lembur_seq'::regclass);


--
-- TOC entry 5052 (class 2604 OID 16913)
-- Name: detail_millages id_detail_millage; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_millages ALTER COLUMN id_detail_millage SET DEFAULT nextval('public.detail_millages_id_detail_millage_seq'::regclass);


--
-- TOC entry 5055 (class 2604 OID 16914)
-- Name: detail_tugasluars id_detail_tugasluar; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars ALTER COLUMN id_detail_tugasluar SET DEFAULT nextval('public.detail_tugasluars_id_detail_tugasluar_seq'::regclass);


--
-- TOC entry 5058 (class 2604 OID 16915)
-- Name: divisis id_divisi; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.divisis ALTER COLUMN id_divisi SET DEFAULT nextval('public.divisis_id_divisi_seq'::regclass);


--
-- TOC entry 5059 (class 2604 OID 16916)
-- Name: events id_event; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.events ALTER COLUMN id_event SET DEFAULT nextval('public.events_id_event_seq'::regclass);


--
-- TOC entry 5060 (class 2604 OID 16917)
-- Name: export_slip_lemburs id_export_slip_lembur; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.export_slip_lemburs ALTER COLUMN id_export_slip_lembur SET DEFAULT nextval('public.export_slip_lemburs_id_export_slip_lembur_seq'::regclass);


--
-- TOC entry 5062 (class 2604 OID 16918)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 5064 (class 2604 OID 16919)
-- Name: gaji_departemens id_gaji_departemen; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.gaji_departemens ALTER COLUMN id_gaji_departemen SET DEFAULT nextval('public.gaji_departemens_id_gaji_departemen_seq'::regclass);


--
-- TOC entry 5068 (class 2604 OID 16920)
-- Name: grup_patterns id_grup_pattern; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grup_patterns ALTER COLUMN id_grup_pattern SET DEFAULT nextval('public.grup_patterns_id_grup_pattern_seq'::regclass);


--
-- TOC entry 5069 (class 2604 OID 16921)
-- Name: grups id_grup; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grups ALTER COLUMN id_grup SET DEFAULT nextval('public.grups_id_grup_seq'::regclass);


--
-- TOC entry 5074 (class 2604 OID 16922)
-- Name: jabatans id_jabatan; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jabatans ALTER COLUMN id_jabatan SET DEFAULT nextval('public.jabatans_id_jabatan_seq'::regclass);


--
-- TOC entry 5075 (class 2604 OID 16923)
-- Name: jenis_cutis id_jenis_cuti; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jenis_cutis ALTER COLUMN id_jenis_cuti SET DEFAULT nextval('public.jenis_cutis_id_jenis_cuti_seq'::regclass);


--
-- TOC entry 5078 (class 2604 OID 16924)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- TOC entry 5079 (class 2604 OID 16925)
-- Name: karyawan_posisi id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawan_posisi ALTER COLUMN id SET DEFAULT nextval('public.karyawan_posisi_id_seq'::regclass);


--
-- TOC entry 5088 (class 2604 OID 16926)
-- Name: ksk_change_histories id_ksk_change_history; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_change_histories ALTER COLUMN id_ksk_change_history SET DEFAULT nextval('public.ksk_change_histories_id_ksk_change_history_seq'::regclass);


--
-- TOC entry 5089 (class 2604 OID 16927)
-- Name: ksk_details id_ksk_detail; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details ALTER COLUMN id_ksk_detail SET DEFAULT nextval('public.ksk_details_id_ksk_detail_seq'::regclass);


--
-- TOC entry 5095 (class 2604 OID 16928)
-- Name: lembur_harians id_lembur_harian; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lembur_harians ALTER COLUMN id_lembur_harian SET DEFAULT nextval('public.lembur_harians_id_lembur_harian_seq'::regclass);


--
-- TOC entry 5102 (class 2604 OID 16929)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 5104 (class 2604 OID 16930)
-- Name: organisasis id_organisasi; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.organisasis ALTER COLUMN id_organisasi SET DEFAULT nextval('public.organisasis_id_organisasi_seq'::regclass);


--
-- TOC entry 5105 (class 2604 OID 16931)
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- TOC entry 5106 (class 2604 OID 16932)
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- TOC entry 5107 (class 2604 OID 16933)
-- Name: pikets id_piket; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.pikets ALTER COLUMN id_piket SET DEFAULT nextval('public.pikets_id_piket_seq'::regclass);


--
-- TOC entry 5108 (class 2604 OID 16934)
-- Name: posisis id_posisi; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.posisis ALTER COLUMN id_posisi SET DEFAULT nextval('public.posisis_id_posisi_seq'::regclass);


--
-- TOC entry 5151 (class 2604 OID 33895)
-- Name: rekap_lembur id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur ALTER COLUMN id SET DEFAULT nextval('public.rekap_lembur_id_seq'::regclass);


--
-- TOC entry 5153 (class 2604 OID 33907)
-- Name: rekap_lembur_summary id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur_summary ALTER COLUMN id SET DEFAULT nextval('public.rekap_lembur_summary_id_seq'::regclass);


--
-- TOC entry 5109 (class 2604 OID 16935)
-- Name: reset_cutis id_reset_cuti; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.reset_cutis ALTER COLUMN id_reset_cuti SET DEFAULT nextval('public.reset_cutis_id_reset_cuti_seq'::regclass);


--
-- TOC entry 5111 (class 2604 OID 16936)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 5112 (class 2604 OID 16937)
-- Name: sakits id_sakit; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sakits ALTER COLUMN id_sakit SET DEFAULT nextval('public.sakits_id_sakit_seq'::regclass);


--
-- TOC entry 5114 (class 2604 OID 16938)
-- Name: seksis id_seksi; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.seksis ALTER COLUMN id_seksi SET DEFAULT nextval('public.seksis_id_seksi_seq'::regclass);


--
-- TOC entry 5115 (class 2604 OID 16939)
-- Name: setting_lembur_karyawans id_setting_lembur_karyawan; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans ALTER COLUMN id_setting_lembur_karyawan SET DEFAULT nextval('public.setting_lembur_karyawans_id_setting_lembur_karyawan_seq'::regclass);


--
-- TOC entry 5117 (class 2604 OID 16940)
-- Name: setting_lemburs id_setting_lembur; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lemburs ALTER COLUMN id_setting_lembur SET DEFAULT nextval('public.setting_lemburs_id_setting_lembur_seq'::regclass);


--
-- TOC entry 5118 (class 2604 OID 16941)
-- Name: setting_tugasluars id_setting_tugasluar; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_tugasluars ALTER COLUMN id_setting_tugasluar SET DEFAULT nextval('public.setting_tugasluars_id_setting_tugasluar_seq'::regclass);


--
-- TOC entry 5142 (class 2604 OID 33878)
-- Name: slip_lembur_karyawans id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.slip_lembur_karyawans ALTER COLUMN id SET DEFAULT nextval('public.slip_lembur_karyawans_id_seq'::regclass);


--
-- TOC entry 5119 (class 2604 OID 16942)
-- Name: sto_headers id_sto_header; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_headers ALTER COLUMN id_sto_header SET DEFAULT nextval('public.sto_headers_id_sto_header_seq'::regclass);


--
-- TOC entry 5121 (class 2604 OID 16943)
-- Name: sto_lines id_sto_line; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_lines ALTER COLUMN id_sto_line SET DEFAULT nextval('public.sto_lines_id_sto_line_seq'::regclass);


--
-- TOC entry 5123 (class 2604 OID 16944)
-- Name: sto_upload id_sto_upload; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_upload ALTER COLUMN id_sto_upload SET DEFAULT nextval('public.sto_upload_id_sto_upload_seq'::regclass);


--
-- TOC entry 5128 (class 2604 OID 16945)
-- Name: templates id_template; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.templates ALTER COLUMN id_template SET DEFAULT nextval('public.templates_id_template_seq'::regclass);


--
-- TOC entry 5140 (class 2604 OID 16946)
-- Name: turnovers id_turnover; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.turnovers ALTER COLUMN id_turnover SET DEFAULT nextval('public.turnovers_id_turnover_seq'::regclass);


--
-- TOC entry 5141 (class 2604 OID 16947)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 5181 (class 2606 OID 16969)
-- Name: activity_log activity_log_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.activity_log
    ADD CONSTRAINT activity_log_pkey PRIMARY KEY (id);


--
-- TOC entry 5185 (class 2606 OID 16971)
-- Name: approval_cutis approval_cutis_cuti_id_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.approval_cutis
    ADD CONSTRAINT approval_cutis_cuti_id_unique UNIQUE (cuti_id);


--
-- TOC entry 5187 (class 2606 OID 16973)
-- Name: approval_cutis approval_cutis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.approval_cutis
    ADD CONSTRAINT approval_cutis_pkey PRIMARY KEY (id_approval_cuti);


--
-- TOC entry 5189 (class 2606 OID 16975)
-- Name: attachment_ksk_details attachment_ksk_details_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_ksk_details
    ADD CONSTRAINT attachment_ksk_details_pkey PRIMARY KEY (id_attachment_ksk_detail);


--
-- TOC entry 5191 (class 2606 OID 16977)
-- Name: attachment_lemburs attachment_lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_lemburs
    ADD CONSTRAINT attachment_lemburs_pkey PRIMARY KEY (id_attachment_lembur);


--
-- TOC entry 5193 (class 2606 OID 16979)
-- Name: attendance_devices attendance_devices_cloud_id_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices
    ADD CONSTRAINT attendance_devices_cloud_id_unique UNIQUE (cloud_id);


--
-- TOC entry 5195 (class 2606 OID 16981)
-- Name: attendance_devices attendance_devices_device_sn_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices
    ADD CONSTRAINT attendance_devices_device_sn_unique UNIQUE (device_sn);


--
-- TOC entry 5197 (class 2606 OID 16983)
-- Name: attendance_devices attendance_devices_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices
    ADD CONSTRAINT attendance_devices_pkey PRIMARY KEY (id_device);


--
-- TOC entry 5199 (class 2606 OID 16985)
-- Name: attendance_gps attendance_gps_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_gps
    ADD CONSTRAINT attendance_gps_pkey PRIMARY KEY (id_att_gps);


--
-- TOC entry 5201 (class 2606 OID 16987)
-- Name: attendance_karyawan_grup attendance_karyawan_grup_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_karyawan_grup
    ADD CONSTRAINT attendance_karyawan_grup_pkey PRIMARY KEY (id);


--
-- TOC entry 5203 (class 2606 OID 16989)
-- Name: attendance_scanlogs attendance_scanlogs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_scanlogs
    ADD CONSTRAINT attendance_scanlogs_pkey PRIMARY KEY (id_scanlog);


--
-- TOC entry 5205 (class 2606 OID 16991)
-- Name: attendance_summaries attendance_summaries_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_summaries
    ADD CONSTRAINT attendance_summaries_pkey PRIMARY KEY (id_att_summary);


--
-- TOC entry 5209 (class 2606 OID 16993)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 5207 (class 2606 OID 16995)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 5211 (class 2606 OID 16997)
-- Name: cleareance_details cleareance_details_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_details
    ADD CONSTRAINT cleareance_details_pkey PRIMARY KEY (id_cleareance_detail);


--
-- TOC entry 5213 (class 2606 OID 16999)
-- Name: cleareance_settings cleareance_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_settings
    ADD CONSTRAINT cleareance_settings_pkey PRIMARY KEY (id_cleareance_setting);


--
-- TOC entry 5215 (class 2606 OID 17001)
-- Name: cleareances cleareances_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareances
    ADD CONSTRAINT cleareances_pkey PRIMARY KEY (id_cleareance);


--
-- TOC entry 5217 (class 2606 OID 17003)
-- Name: cutis cutis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cutis
    ADD CONSTRAINT cutis_pkey PRIMARY KEY (id_cuti);


--
-- TOC entry 5219 (class 2606 OID 17005)
-- Name: departemens departemens_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.departemens
    ADD CONSTRAINT departemens_pkey PRIMARY KEY (id_departemen);


--
-- TOC entry 5221 (class 2606 OID 17007)
-- Name: detail_lemburs detail_lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs
    ADD CONSTRAINT detail_lemburs_pkey PRIMARY KEY (id_detail_lembur);


--
-- TOC entry 5223 (class 2606 OID 17009)
-- Name: detail_millages detail_millages_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_millages
    ADD CONSTRAINT detail_millages_pkey PRIMARY KEY (id_detail_millage);


--
-- TOC entry 5225 (class 2606 OID 17011)
-- Name: detail_tugasluars detail_tugasluars_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars
    ADD CONSTRAINT detail_tugasluars_pkey PRIMARY KEY (id_detail_tugasluar);


--
-- TOC entry 5227 (class 2606 OID 17013)
-- Name: divisis divisis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.divisis
    ADD CONSTRAINT divisis_pkey PRIMARY KEY (id_divisi);


--
-- TOC entry 5229 (class 2606 OID 17015)
-- Name: events events_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id_event);


--
-- TOC entry 5231 (class 2606 OID 17017)
-- Name: export_slip_lemburs export_slip_lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.export_slip_lemburs
    ADD CONSTRAINT export_slip_lemburs_pkey PRIMARY KEY (id_export_slip_lembur);


--
-- TOC entry 5233 (class 2606 OID 17019)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5235 (class 2606 OID 17021)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 5237 (class 2606 OID 17023)
-- Name: gaji_departemens gaji_departemens_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.gaji_departemens
    ADD CONSTRAINT gaji_departemens_pkey PRIMARY KEY (id_gaji_departemen);


--
-- TOC entry 5239 (class 2606 OID 17025)
-- Name: grup_patterns grup_patterns_nama_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grup_patterns
    ADD CONSTRAINT grup_patterns_nama_unique UNIQUE (nama);


--
-- TOC entry 5241 (class 2606 OID 17027)
-- Name: grup_patterns grup_patterns_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grup_patterns
    ADD CONSTRAINT grup_patterns_pkey PRIMARY KEY (id_grup_pattern);


--
-- TOC entry 5243 (class 2606 OID 17029)
-- Name: grups grups_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grups
    ADD CONSTRAINT grups_pkey PRIMARY KEY (id_grup);


--
-- TOC entry 5245 (class 2606 OID 17031)
-- Name: izins izins_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.izins
    ADD CONSTRAINT izins_pkey PRIMARY KEY (id_izin);


--
-- TOC entry 5247 (class 2606 OID 17033)
-- Name: jabatans jabatans_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jabatans
    ADD CONSTRAINT jabatans_pkey PRIMARY KEY (id_jabatan);


--
-- TOC entry 5249 (class 2606 OID 17035)
-- Name: jenis_cutis jenis_cutis_jenis_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jenis_cutis
    ADD CONSTRAINT jenis_cutis_jenis_unique UNIQUE (jenis);


--
-- TOC entry 5251 (class 2606 OID 17037)
-- Name: jenis_cutis jenis_cutis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jenis_cutis
    ADD CONSTRAINT jenis_cutis_pkey PRIMARY KEY (id_jenis_cuti);


--
-- TOC entry 5253 (class 2606 OID 17039)
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- TOC entry 5255 (class 2606 OID 17041)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5258 (class 2606 OID 17043)
-- Name: karyawan_posisi karyawan_posisi_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawan_posisi
    ADD CONSTRAINT karyawan_posisi_pkey PRIMARY KEY (id);


--
-- TOC entry 5260 (class 2606 OID 17045)
-- Name: karyawans karyawans_email_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawans
    ADD CONSTRAINT karyawans_email_unique UNIQUE (email);


--
-- TOC entry 5262 (class 2606 OID 17047)
-- Name: karyawans karyawans_ni_karyawan_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawans
    ADD CONSTRAINT karyawans_ni_karyawan_unique UNIQUE (ni_karyawan);


--
-- TOC entry 5264 (class 2606 OID 17049)
-- Name: karyawans karyawans_no_telp_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawans
    ADD CONSTRAINT karyawans_no_telp_unique UNIQUE (no_telp);


--
-- TOC entry 5266 (class 2606 OID 17051)
-- Name: karyawans karyawans_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawans
    ADD CONSTRAINT karyawans_pkey PRIMARY KEY (id_karyawan);


--
-- TOC entry 5268 (class 2606 OID 17053)
-- Name: kontraks kontraks_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.kontraks
    ADD CONSTRAINT kontraks_pkey PRIMARY KEY (id_kontrak);


--
-- TOC entry 5272 (class 2606 OID 17055)
-- Name: ksk_change_histories ksk_change_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_change_histories
    ADD CONSTRAINT ksk_change_histories_pkey PRIMARY KEY (id_ksk_change_history);


--
-- TOC entry 5274 (class 2606 OID 17057)
-- Name: ksk_details ksk_details_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details
    ADD CONSTRAINT ksk_details_pkey PRIMARY KEY (id_ksk_detail);


--
-- TOC entry 5270 (class 2606 OID 17059)
-- Name: ksk ksk_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk
    ADD CONSTRAINT ksk_pkey PRIMARY KEY (id_ksk);


--
-- TOC entry 5276 (class 2606 OID 17061)
-- Name: lembur_harians lembur_harians_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lembur_harians
    ADD CONSTRAINT lembur_harians_pkey PRIMARY KEY (id_lembur_harian);


--
-- TOC entry 5278 (class 2606 OID 17063)
-- Name: lemburs lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lemburs
    ADD CONSTRAINT lemburs_pkey PRIMARY KEY (id_lembur);


--
-- TOC entry 5280 (class 2606 OID 17065)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 5282 (class 2606 OID 17067)
-- Name: millages millages_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.millages
    ADD CONSTRAINT millages_pkey PRIMARY KEY (id_millage);


--
-- TOC entry 5285 (class 2606 OID 17069)
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- TOC entry 5288 (class 2606 OID 17071)
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- TOC entry 5290 (class 2606 OID 17073)
-- Name: organisasis organisasis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.organisasis
    ADD CONSTRAINT organisasis_pkey PRIMARY KEY (id_organisasi);


--
-- TOC entry 5292 (class 2606 OID 17075)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 5294 (class 2606 OID 17077)
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- TOC entry 5296 (class 2606 OID 17079)
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- TOC entry 5298 (class 2606 OID 17081)
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 5300 (class 2606 OID 17083)
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- TOC entry 5303 (class 2606 OID 17085)
-- Name: pikets pikets_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.pikets
    ADD CONSTRAINT pikets_pkey PRIMARY KEY (id_piket);


--
-- TOC entry 5305 (class 2606 OID 17087)
-- Name: posisis posisis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.posisis
    ADD CONSTRAINT posisis_pkey PRIMARY KEY (id_posisi);


--
-- TOC entry 5355 (class 2606 OID 33915)
-- Name: rekap_lembur rekap_lembur_karyawan_id_periode_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur
    ADD CONSTRAINT rekap_lembur_karyawan_id_periode_unique UNIQUE (karyawan_id, periode);


--
-- TOC entry 5357 (class 2606 OID 33900)
-- Name: rekap_lembur rekap_lembur_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur
    ADD CONSTRAINT rekap_lembur_pkey PRIMARY KEY (id);


--
-- TOC entry 5359 (class 2606 OID 33913)
-- Name: rekap_lembur_summary rekap_lembur_summary_departemen_periode_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur_summary
    ADD CONSTRAINT rekap_lembur_summary_departemen_periode_unique UNIQUE (departemen, periode);


--
-- TOC entry 5361 (class 2606 OID 33911)
-- Name: rekap_lembur_summary rekap_lembur_summary_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur_summary
    ADD CONSTRAINT rekap_lembur_summary_pkey PRIMARY KEY (id);


--
-- TOC entry 5307 (class 2606 OID 17089)
-- Name: reset_cutis reset_cutis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.reset_cutis
    ADD CONSTRAINT reset_cutis_pkey PRIMARY KEY (id_reset_cuti);


--
-- TOC entry 5309 (class 2606 OID 17091)
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- TOC entry 5311 (class 2606 OID 17093)
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- TOC entry 5313 (class 2606 OID 17095)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 5315 (class 2606 OID 17097)
-- Name: sakits sakits_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sakits
    ADD CONSTRAINT sakits_pkey PRIMARY KEY (id_sakit);


--
-- TOC entry 5317 (class 2606 OID 17099)
-- Name: seksis seksis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.seksis
    ADD CONSTRAINT seksis_pkey PRIMARY KEY (id_seksi);


--
-- TOC entry 5320 (class 2606 OID 17101)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 5323 (class 2606 OID 17103)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_karyawan_id_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_karyawan_id_unique UNIQUE (karyawan_id);


--
-- TOC entry 5325 (class 2606 OID 17105)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_pkey PRIMARY KEY (id_setting_lembur_karyawan);


--
-- TOC entry 5327 (class 2606 OID 17107)
-- Name: setting_lemburs setting_lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lemburs
    ADD CONSTRAINT setting_lemburs_pkey PRIMARY KEY (id_setting_lembur);


--
-- TOC entry 5329 (class 2606 OID 17109)
-- Name: setting_tugasluars setting_tugasluars_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_tugasluars
    ADD CONSTRAINT setting_tugasluars_pkey PRIMARY KEY (id_setting_tugasluar);


--
-- TOC entry 5351 (class 2606 OID 33888)
-- Name: slip_lembur_karyawans slip_lembur_karyawans_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.slip_lembur_karyawans
    ADD CONSTRAINT slip_lembur_karyawans_pkey PRIMARY KEY (id);


--
-- TOC entry 5331 (class 2606 OID 17111)
-- Name: sto_headers sto_headers_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_headers
    ADD CONSTRAINT sto_headers_pkey PRIMARY KEY (id_sto_header);


--
-- TOC entry 5333 (class 2606 OID 17113)
-- Name: sto_lines sto_lines_no_label_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_lines
    ADD CONSTRAINT sto_lines_no_label_unique UNIQUE (no_label);


--
-- TOC entry 5335 (class 2606 OID 17115)
-- Name: sto_lines sto_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_lines
    ADD CONSTRAINT sto_lines_pkey PRIMARY KEY (id_sto_line);


--
-- TOC entry 5337 (class 2606 OID 17117)
-- Name: sto_upload sto_upload_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_upload
    ADD CONSTRAINT sto_upload_pkey PRIMARY KEY (id_sto_upload);


--
-- TOC entry 5339 (class 2606 OID 17119)
-- Name: templates templates_nama_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.templates
    ADD CONSTRAINT templates_nama_unique UNIQUE (nama);


--
-- TOC entry 5341 (class 2606 OID 17121)
-- Name: templates templates_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.templates
    ADD CONSTRAINT templates_pkey PRIMARY KEY (id_template);


--
-- TOC entry 5343 (class 2606 OID 17123)
-- Name: tugasluars tugasluars_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.tugasluars
    ADD CONSTRAINT tugasluars_pkey PRIMARY KEY (id_tugasluar);


--
-- TOC entry 5345 (class 2606 OID 17125)
-- Name: turnovers turnovers_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.turnovers
    ADD CONSTRAINT turnovers_pkey PRIMARY KEY (id_turnover);


--
-- TOC entry 5353 (class 2606 OID 33890)
-- Name: slip_lembur_karyawans unique_slip_lembur; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.slip_lembur_karyawans
    ADD CONSTRAINT unique_slip_lembur UNIQUE (karyawan_id, periode, organisasi_id);


--
-- TOC entry 5347 (class 2606 OID 17127)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 5349 (class 2606 OID 17129)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 5179 (class 1259 OID 17130)
-- Name: activity_log_log_name_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX activity_log_log_name_index ON public.activity_log USING btree (log_name);


--
-- TOC entry 5182 (class 1259 OID 17131)
-- Name: causer; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX causer ON public.activity_log USING btree (causer_type, causer_id);


--
-- TOC entry 5256 (class 1259 OID 17132)
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- TOC entry 5283 (class 1259 OID 17133)
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- TOC entry 5286 (class 1259 OID 17134)
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- TOC entry 5301 (class 1259 OID 17135)
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- TOC entry 5318 (class 1259 OID 17136)
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- TOC entry 5321 (class 1259 OID 17137)
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- TOC entry 5183 (class 1259 OID 17138)
-- Name: subject; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX subject ON public.activity_log USING btree (subject_type, subject_id);


--
-- TOC entry 5362 (class 2606 OID 17139)
-- Name: approval_cutis approval_cutis_cuti_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.approval_cutis
    ADD CONSTRAINT approval_cutis_cuti_id_foreign FOREIGN KEY (cuti_id) REFERENCES public.cutis(id_cuti) ON DELETE CASCADE;


--
-- TOC entry 5363 (class 2606 OID 17144)
-- Name: attachment_ksk_details attachment_ksk_details_ksk_detail_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_ksk_details
    ADD CONSTRAINT attachment_ksk_details_ksk_detail_id_foreign FOREIGN KEY (ksk_detail_id) REFERENCES public.ksk_details(id_ksk_detail) ON DELETE CASCADE;


--
-- TOC entry 5364 (class 2606 OID 17149)
-- Name: attachment_lemburs attachment_lemburs_lembur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_lemburs
    ADD CONSTRAINT attachment_lemburs_lembur_id_foreign FOREIGN KEY (lembur_id) REFERENCES public.lemburs(id_lembur) ON DELETE CASCADE;


--
-- TOC entry 5365 (class 2606 OID 17154)
-- Name: attendance_devices attendance_devices_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices
    ADD CONSTRAINT attendance_devices_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5366 (class 2606 OID 17159)
-- Name: attendance_gps attendance_gps_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_gps
    ADD CONSTRAINT attendance_gps_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5367 (class 2606 OID 17164)
-- Name: attendance_gps attendance_gps_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_gps
    ADD CONSTRAINT attendance_gps_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5368 (class 2606 OID 17169)
-- Name: attendance_karyawan_grup attendance_karyawan_grup_grup_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_karyawan_grup
    ADD CONSTRAINT attendance_karyawan_grup_grup_id_foreign FOREIGN KEY (grup_id) REFERENCES public.grups(id_grup) ON DELETE RESTRICT;


--
-- TOC entry 5369 (class 2606 OID 17174)
-- Name: attendance_karyawan_grup attendance_karyawan_grup_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_karyawan_grup
    ADD CONSTRAINT attendance_karyawan_grup_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5370 (class 2606 OID 17179)
-- Name: attendance_scanlogs attendance_scanlogs_device_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_scanlogs
    ADD CONSTRAINT attendance_scanlogs_device_id_foreign FOREIGN KEY (device_id) REFERENCES public.attendance_devices(id_device) ON DELETE RESTRICT;


--
-- TOC entry 5371 (class 2606 OID 17184)
-- Name: attendance_scanlogs attendance_scanlogs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_scanlogs
    ADD CONSTRAINT attendance_scanlogs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5372 (class 2606 OID 17189)
-- Name: attendance_summaries attendance_summaries_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_summaries
    ADD CONSTRAINT attendance_summaries_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5373 (class 2606 OID 17194)
-- Name: attendance_summaries attendance_summaries_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_summaries
    ADD CONSTRAINT attendance_summaries_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5374 (class 2606 OID 17199)
-- Name: cleareance_details cleareance_details_cleareance_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_details
    ADD CONSTRAINT cleareance_details_cleareance_id_foreign FOREIGN KEY (cleareance_id) REFERENCES public.cleareances(id_cleareance) ON DELETE RESTRICT;


--
-- TOC entry 5375 (class 2606 OID 17204)
-- Name: cleareance_details cleareance_details_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_details
    ADD CONSTRAINT cleareance_details_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5376 (class 2606 OID 17209)
-- Name: cleareance_settings cleareance_settings_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_settings
    ADD CONSTRAINT cleareance_settings_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5377 (class 2606 OID 17214)
-- Name: cleareances cleareances_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareances
    ADD CONSTRAINT cleareances_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE CASCADE;


--
-- TOC entry 5378 (class 2606 OID 17219)
-- Name: cleareances cleareances_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareances
    ADD CONSTRAINT cleareances_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE CASCADE;


--
-- TOC entry 5379 (class 2606 OID 17224)
-- Name: cutis cutis_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cutis
    ADD CONSTRAINT cutis_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5380 (class 2606 OID 17229)
-- Name: departemens departemens_divisi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.departemens
    ADD CONSTRAINT departemens_divisi_id_foreign FOREIGN KEY (divisi_id) REFERENCES public.divisis(id_divisi) ON DELETE RESTRICT;


--
-- TOC entry 5381 (class 2606 OID 17234)
-- Name: detail_lemburs detail_lemburs_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs
    ADD CONSTRAINT detail_lemburs_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5382 (class 2606 OID 17239)
-- Name: detail_lemburs detail_lemburs_lembur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs
    ADD CONSTRAINT detail_lemburs_lembur_id_foreign FOREIGN KEY (lembur_id) REFERENCES public.lemburs(id_lembur) ON DELETE RESTRICT;


--
-- TOC entry 5383 (class 2606 OID 17244)
-- Name: detail_lemburs detail_lemburs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs
    ADD CONSTRAINT detail_lemburs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5384 (class 2606 OID 17249)
-- Name: detail_millages detail_millages_millage_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_millages
    ADD CONSTRAINT detail_millages_millage_id_foreign FOREIGN KEY (millage_id) REFERENCES public.millages(id_millage) ON DELETE RESTRICT;


--
-- TOC entry 5385 (class 2606 OID 17254)
-- Name: detail_millages detail_millages_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_millages
    ADD CONSTRAINT detail_millages_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5386 (class 2606 OID 17259)
-- Name: detail_tugasluars detail_tugasluars_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars
    ADD CONSTRAINT detail_tugasluars_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5387 (class 2606 OID 17264)
-- Name: detail_tugasluars detail_tugasluars_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars
    ADD CONSTRAINT detail_tugasluars_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5388 (class 2606 OID 17269)
-- Name: detail_tugasluars detail_tugasluars_tugasluar_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars
    ADD CONSTRAINT detail_tugasluars_tugasluar_id_foreign FOREIGN KEY (tugasluar_id) REFERENCES public.tugasluars(id_tugasluar) ON DELETE RESTRICT;


--
-- TOC entry 5389 (class 2606 OID 17274)
-- Name: export_slip_lemburs export_slip_lemburs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.export_slip_lemburs
    ADD CONSTRAINT export_slip_lemburs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5390 (class 2606 OID 17279)
-- Name: gaji_departemens gaji_departemens_departemen_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.gaji_departemens
    ADD CONSTRAINT gaji_departemens_departemen_id_foreign FOREIGN KEY (departemen_id) REFERENCES public.departemens(id_departemen) ON DELETE RESTRICT;


--
-- TOC entry 5391 (class 2606 OID 17284)
-- Name: gaji_departemens gaji_departemens_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.gaji_departemens
    ADD CONSTRAINT gaji_departemens_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE CASCADE;


--
-- TOC entry 5392 (class 2606 OID 17289)
-- Name: grup_patterns grup_patterns_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grup_patterns
    ADD CONSTRAINT grup_patterns_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5393 (class 2606 OID 17294)
-- Name: izins izins_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.izins
    ADD CONSTRAINT izins_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5394 (class 2606 OID 17299)
-- Name: izins izins_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.izins
    ADD CONSTRAINT izins_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5395 (class 2606 OID 17304)
-- Name: karyawan_posisi karyawan_posisi_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawan_posisi
    ADD CONSTRAINT karyawan_posisi_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE CASCADE;


--
-- TOC entry 5396 (class 2606 OID 17309)
-- Name: karyawan_posisi karyawan_posisi_posisi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawan_posisi
    ADD CONSTRAINT karyawan_posisi_posisi_id_foreign FOREIGN KEY (posisi_id) REFERENCES public.posisis(id_posisi) ON DELETE CASCADE;


--
-- TOC entry 5397 (class 2606 OID 17314)
-- Name: kontraks kontraks_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.kontraks
    ADD CONSTRAINT kontraks_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5399 (class 2606 OID 17319)
-- Name: ksk_change_histories ksk_change_histories_changed_by_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_change_histories
    ADD CONSTRAINT ksk_change_histories_changed_by_id_foreign FOREIGN KEY (changed_by_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5400 (class 2606 OID 17324)
-- Name: ksk_change_histories ksk_change_histories_ksk_detail_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_change_histories
    ADD CONSTRAINT ksk_change_histories_ksk_detail_id_foreign FOREIGN KEY (ksk_detail_id) REFERENCES public.ksk_details(id_ksk_detail) ON DELETE RESTRICT;


--
-- TOC entry 5401 (class 2606 OID 17329)
-- Name: ksk_details ksk_details_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details
    ADD CONSTRAINT ksk_details_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5402 (class 2606 OID 17334)
-- Name: ksk_details ksk_details_ksk_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details
    ADD CONSTRAINT ksk_details_ksk_id_foreign FOREIGN KEY (ksk_id) REFERENCES public.ksk(id_ksk) ON DELETE RESTRICT;


--
-- TOC entry 5403 (class 2606 OID 17339)
-- Name: ksk_details ksk_details_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details
    ADD CONSTRAINT ksk_details_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5398 (class 2606 OID 17344)
-- Name: ksk ksk_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk
    ADD CONSTRAINT ksk_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5404 (class 2606 OID 17349)
-- Name: lembur_harians lembur_harians_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lembur_harians
    ADD CONSTRAINT lembur_harians_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5405 (class 2606 OID 17354)
-- Name: lemburs lemburs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lemburs
    ADD CONSTRAINT lemburs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5406 (class 2606 OID 17359)
-- Name: millages millages_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.millages
    ADD CONSTRAINT millages_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5407 (class 2606 OID 17364)
-- Name: millages millages_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.millages
    ADD CONSTRAINT millages_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5408 (class 2606 OID 17369)
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- TOC entry 5409 (class 2606 OID 17374)
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- TOC entry 5410 (class 2606 OID 17379)
-- Name: pikets pikets_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.pikets
    ADD CONSTRAINT pikets_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5411 (class 2606 OID 17384)
-- Name: pikets pikets_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.pikets
    ADD CONSTRAINT pikets_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5412 (class 2606 OID 17389)
-- Name: posisis posisis_jabatan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.posisis
    ADD CONSTRAINT posisis_jabatan_id_foreign FOREIGN KEY (jabatan_id) REFERENCES public.jabatans(id_jabatan) ON DELETE RESTRICT;


--
-- TOC entry 5413 (class 2606 OID 17394)
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- TOC entry 5414 (class 2606 OID 17399)
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- TOC entry 5415 (class 2606 OID 17404)
-- Name: sakits sakits_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sakits
    ADD CONSTRAINT sakits_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5416 (class 2606 OID 17409)
-- Name: sakits sakits_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sakits
    ADD CONSTRAINT sakits_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5417 (class 2606 OID 17414)
-- Name: seksis seksis_departemen_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.seksis
    ADD CONSTRAINT seksis_departemen_id_foreign FOREIGN KEY (departemen_id) REFERENCES public.departemens(id_departemen) ON DELETE RESTRICT;


--
-- TOC entry 5418 (class 2606 OID 17419)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_jabatan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_jabatan_id_foreign FOREIGN KEY (jabatan_id) REFERENCES public.jabatans(id_jabatan) ON DELETE RESTRICT;


--
-- TOC entry 5419 (class 2606 OID 17424)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5420 (class 2606 OID 17429)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5421 (class 2606 OID 17434)
-- Name: setting_lemburs setting_lemburs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lemburs
    ADD CONSTRAINT setting_lemburs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5422 (class 2606 OID 17439)
-- Name: setting_tugasluars setting_tugasluars_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_tugasluars
    ADD CONSTRAINT setting_tugasluars_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5423 (class 2606 OID 17444)
-- Name: sto_lines sto_lines_sto_header_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_lines
    ADD CONSTRAINT sto_lines_sto_header_id_foreign FOREIGN KEY (sto_header_id) REFERENCES public.sto_headers(id_sto_header) ON DELETE CASCADE;


--
-- TOC entry 5424 (class 2606 OID 17449)
-- Name: tugasluars tugasluars_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.tugasluars
    ADD CONSTRAINT tugasluars_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5425 (class 2606 OID 17454)
-- Name: tugasluars tugasluars_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.tugasluars
    ADD CONSTRAINT tugasluars_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5426 (class 2606 OID 17459)
-- Name: turnovers turnovers_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.turnovers
    ADD CONSTRAINT turnovers_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


-- Completed on 2025-09-10 20:05:10

--
-- PostgreSQL database dump complete
--

--
-- Database "postgres" dump
--

\connect postgres

--
-- PostgreSQL database dump
--

-- Dumped from database version 16.9
-- Dumped by pg_dump version 16.9

-- Started on 2025-09-10 20:05:10

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SELECT pg_catalog.set_config('search_path', '', false);
SET check_function_bodies = false;
SET xmloption = content;
SET client_min_messages = warning;
SET row_security = off;

--
-- TOC entry 2 (class 3079 OID 16384)
-- Name: adminpack; Type: EXTENSION; Schema: -; Owner: -
--

CREATE EXTENSION IF NOT EXISTS adminpack WITH SCHEMA pg_catalog;


--
-- TOC entry 4778 (class 0 OID 0)
-- Dependencies: 2
-- Name: EXTENSION adminpack; Type: COMMENT; Schema: -; Owner: 
--

COMMENT ON EXTENSION adminpack IS 'administrative functions for PostgreSQL';


-- Completed on 2025-09-10 20:05:10

--
-- PostgreSQL database dump complete
--

-- Completed on 2025-09-10 20:05:10

--
-- PostgreSQL database cluster dump complete
--

