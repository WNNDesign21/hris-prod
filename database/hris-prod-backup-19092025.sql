--
-- PostgreSQL database dump
--

-- Dumped from database version 16.9
-- Dumped by pg_dump version 17.5

-- Started on 2025-09-20 17:28:23

SET statement_timeout = 0;
SET lock_timeout = 0;
SET idle_in_transaction_session_timeout = 0;
SET transaction_timeout = 0;
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
-- TOC entry 5695 (class 0 OID 0)
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
-- TOC entry 5696 (class 0 OID 0)
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
-- TOC entry 5697 (class 0 OID 0)
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
-- TOC entry 5698 (class 0 OID 0)
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
-- TOC entry 5699 (class 0 OID 0)
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
-- TOC entry 5700 (class 0 OID 0)
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
-- TOC entry 5701 (class 0 OID 0)
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
-- TOC entry 5702 (class 0 OID 0)
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
-- TOC entry 5703 (class 0 OID 0)
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
-- TOC entry 5704 (class 0 OID 0)
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
-- TOC entry 5705 (class 0 OID 0)
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
-- TOC entry 5706 (class 0 OID 0)
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
-- TOC entry 5707 (class 0 OID 0)
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
-- TOC entry 5708 (class 0 OID 0)
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
-- TOC entry 5709 (class 0 OID 0)
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
-- TOC entry 5710 (class 0 OID 0)
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
-- TOC entry 5711 (class 0 OID 0)
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
-- TOC entry 5712 (class 0 OID 0)
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
-- TOC entry 5713 (class 0 OID 0)
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
-- TOC entry 5714 (class 0 OID 0)
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
-- TOC entry 5715 (class 0 OID 0)
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
-- TOC entry 5716 (class 0 OID 0)
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
-- TOC entry 5717 (class 0 OID 0)
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
-- TOC entry 5718 (class 0 OID 0)
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
-- TOC entry 5719 (class 0 OID 0)
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
-- TOC entry 5720 (class 0 OID 0)
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
-- TOC entry 5721 (class 0 OID 0)
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
-- TOC entry 5722 (class 0 OID 0)
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
-- TOC entry 5723 (class 0 OID 0)
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
-- TOC entry 5724 (class 0 OID 0)
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
-- TOC entry 5725 (class 0 OID 0)
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
-- TOC entry 5726 (class 0 OID 0)
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
-- TOC entry 5727 (class 0 OID 0)
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
-- TOC entry 5728 (class 0 OID 0)
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
-- TOC entry 5729 (class 0 OID 0)
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
-- TOC entry 5730 (class 0 OID 0)
-- Dependencies: 299
-- Name: posisis_id_posisi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.posisis_id_posisi_seq OWNED BY public.posisis.id_posisi;


--
-- TOC entry 300 (class 1259 OID 16792)
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
-- TOC entry 301 (class 1259 OID 16798)
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
-- TOC entry 5731 (class 0 OID 0)
-- Dependencies: 301
-- Name: rekap_lembur_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.rekap_lembur_id_seq OWNED BY public.rekap_lembur.id;


--
-- TOC entry 302 (class 1259 OID 16799)
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
-- TOC entry 303 (class 1259 OID 16805)
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
-- TOC entry 5732 (class 0 OID 0)
-- Dependencies: 303
-- Name: rekap_lembur_summary_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.rekap_lembur_summary_id_seq OWNED BY public.rekap_lembur_summary.id;


--
-- TOC entry 304 (class 1259 OID 16806)
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
-- TOC entry 305 (class 1259 OID 16810)
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
-- TOC entry 5733 (class 0 OID 0)
-- Dependencies: 305
-- Name: reset_cutis_id_reset_cuti_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.reset_cutis_id_reset_cuti_seq OWNED BY public.reset_cutis.id_reset_cuti;


--
-- TOC entry 306 (class 1259 OID 16811)
-- Name: role_has_permissions; Type: TABLE; Schema: public; Owner: ict
--

CREATE TABLE public.role_has_permissions (
    permission_id bigint NOT NULL,
    role_id bigint NOT NULL
);


ALTER TABLE public.role_has_permissions OWNER TO ict;

--
-- TOC entry 307 (class 1259 OID 16814)
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
-- TOC entry 308 (class 1259 OID 16819)
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
-- TOC entry 5734 (class 0 OID 0)
-- Dependencies: 308
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- TOC entry 309 (class 1259 OID 16820)
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
-- TOC entry 310 (class 1259 OID 16826)
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
-- TOC entry 5735 (class 0 OID 0)
-- Dependencies: 310
-- Name: sakits_id_sakit_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.sakits_id_sakit_seq OWNED BY public.sakits.id_sakit;


--
-- TOC entry 311 (class 1259 OID 16827)
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
-- TOC entry 312 (class 1259 OID 16830)
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
-- TOC entry 5736 (class 0 OID 0)
-- Dependencies: 312
-- Name: seksis_id_seksi_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.seksis_id_seksi_seq OWNED BY public.seksis.id_seksi;


--
-- TOC entry 313 (class 1259 OID 16831)
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
-- TOC entry 314 (class 1259 OID 16836)
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
-- TOC entry 315 (class 1259 OID 16840)
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
-- TOC entry 5737 (class 0 OID 0)
-- Dependencies: 315
-- Name: setting_lembur_karyawans_id_setting_lembur_karyawan_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.setting_lembur_karyawans_id_setting_lembur_karyawan_seq OWNED BY public.setting_lembur_karyawans.id_setting_lembur_karyawan;


--
-- TOC entry 316 (class 1259 OID 16841)
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
-- TOC entry 317 (class 1259 OID 16846)
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
-- TOC entry 5738 (class 0 OID 0)
-- Dependencies: 317
-- Name: setting_lemburs_id_setting_lembur_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.setting_lemburs_id_setting_lembur_seq OWNED BY public.setting_lemburs.id_setting_lembur;


--
-- TOC entry 318 (class 1259 OID 16847)
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
-- TOC entry 319 (class 1259 OID 16852)
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
-- TOC entry 5739 (class 0 OID 0)
-- Dependencies: 319
-- Name: setting_tugasluars_id_setting_tugasluar_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.setting_tugasluars_id_setting_tugasluar_seq OWNED BY public.setting_tugasluars.id_setting_tugasluar;


--
-- TOC entry 320 (class 1259 OID 16853)
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
-- TOC entry 321 (class 1259 OID 16864)
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
-- TOC entry 5740 (class 0 OID 0)
-- Dependencies: 321
-- Name: slip_lembur_karyawans_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.slip_lembur_karyawans_id_seq OWNED BY public.slip_lembur_karyawans.id;


--
-- TOC entry 322 (class 1259 OID 16865)
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
-- TOC entry 323 (class 1259 OID 16871)
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
-- TOC entry 5741 (class 0 OID 0)
-- Dependencies: 323
-- Name: sto_headers_id_sto_header_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.sto_headers_id_sto_header_seq OWNED BY public.sto_headers.id_sto_header;


--
-- TOC entry 324 (class 1259 OID 16872)
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
-- TOC entry 325 (class 1259 OID 16878)
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
-- TOC entry 5742 (class 0 OID 0)
-- Dependencies: 325
-- Name: sto_lines_id_sto_line_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.sto_lines_id_sto_line_seq OWNED BY public.sto_lines.id_sto_line;


--
-- TOC entry 326 (class 1259 OID 16879)
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
-- TOC entry 327 (class 1259 OID 16888)
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
-- TOC entry 5743 (class 0 OID 0)
-- Dependencies: 327
-- Name: sto_upload_id_sto_upload_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.sto_upload_id_sto_upload_seq OWNED BY public.sto_upload.id_sto_upload;


--
-- TOC entry 328 (class 1259 OID 16889)
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
-- TOC entry 329 (class 1259 OID 16896)
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
-- TOC entry 5744 (class 0 OID 0)
-- Dependencies: 329
-- Name: templates_id_template_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.templates_id_template_seq OWNED BY public.templates.id_template;


--
-- TOC entry 330 (class 1259 OID 16897)
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
-- TOC entry 331 (class 1259 OID 16912)
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
-- TOC entry 332 (class 1259 OID 16918)
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
-- TOC entry 5745 (class 0 OID 0)
-- Dependencies: 332
-- Name: turnovers_id_turnover_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.turnovers_id_turnover_seq OWNED BY public.turnovers.id_turnover;


--
-- TOC entry 333 (class 1259 OID 16919)
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
-- TOC entry 334 (class 1259 OID 16924)
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
-- TOC entry 5746 (class 0 OID 0)
-- Dependencies: 334
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: ict
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- TOC entry 4953 (class 2604 OID 32768)
-- Name: activity_log id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.activity_log ALTER COLUMN id SET DEFAULT nextval('public.activity_log_id_seq'::regclass);


--
-- TOC entry 4954 (class 2604 OID 32769)
-- Name: approval_cutis id_approval_cuti; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.approval_cutis ALTER COLUMN id_approval_cuti SET DEFAULT nextval('public.approval_cutis_id_approval_cuti_seq'::regclass);


--
-- TOC entry 4955 (class 2604 OID 32770)
-- Name: attachment_ksk_details id_attachment_ksk_detail; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_ksk_details ALTER COLUMN id_attachment_ksk_detail SET DEFAULT nextval('public.attachment_ksk_details_id_attachment_ksk_detail_seq'::regclass);


--
-- TOC entry 4956 (class 2604 OID 32771)
-- Name: attachment_lemburs id_attachment_lembur; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_lemburs ALTER COLUMN id_attachment_lembur SET DEFAULT nextval('public.attachment_lemburs_id_attachment_lembur_seq'::regclass);


--
-- TOC entry 4957 (class 2604 OID 32772)
-- Name: attendance_devices id_device; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices ALTER COLUMN id_device SET DEFAULT nextval('public.attendance_devices_id_device_seq'::regclass);


--
-- TOC entry 4958 (class 2604 OID 32773)
-- Name: attendance_gps id_att_gps; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_gps ALTER COLUMN id_att_gps SET DEFAULT nextval('public.attendance_gps_id_att_gps_seq'::regclass);


--
-- TOC entry 4959 (class 2604 OID 32774)
-- Name: attendance_karyawan_grup id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_karyawan_grup ALTER COLUMN id SET DEFAULT nextval('public.attendance_karyawan_grup_id_seq'::regclass);


--
-- TOC entry 4963 (class 2604 OID 32775)
-- Name: attendance_scanlogs id_scanlog; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_scanlogs ALTER COLUMN id_scanlog SET DEFAULT nextval('public.attendance_scanlogs_id_scanlog_seq'::regclass);


--
-- TOC entry 4964 (class 2604 OID 32776)
-- Name: attendance_summaries id_att_summary; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_summaries ALTER COLUMN id_att_summary SET DEFAULT nextval('public.attendance_summaries_id_att_summary_seq'::regclass);


--
-- TOC entry 5033 (class 2604 OID 32777)
-- Name: cleareance_details id_cleareance_detail; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_details ALTER COLUMN id_cleareance_detail SET DEFAULT nextval('public.cleareance_details_id_cleareance_detail_seq'::regclass);


--
-- TOC entry 5035 (class 2604 OID 32778)
-- Name: cleareance_settings id_cleareance_setting; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_settings ALTER COLUMN id_cleareance_setting SET DEFAULT nextval('public.cleareance_settings_id_cleareance_setting_seq'::regclass);


--
-- TOC entry 5037 (class 2604 OID 32779)
-- Name: cutis id_cuti; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cutis ALTER COLUMN id_cuti SET DEFAULT nextval('public.cutis_id_cuti_seq'::regclass);


--
-- TOC entry 5041 (class 2604 OID 32780)
-- Name: departemens id_departemen; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.departemens ALTER COLUMN id_departemen SET DEFAULT nextval('public.departemens_id_departemen_seq'::regclass);


--
-- TOC entry 5042 (class 2604 OID 32781)
-- Name: detail_lemburs id_detail_lembur; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs ALTER COLUMN id_detail_lembur SET DEFAULT nextval('public.detail_lemburs_id_detail_lembur_seq'::regclass);


--
-- TOC entry 5052 (class 2604 OID 32782)
-- Name: detail_millages id_detail_millage; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_millages ALTER COLUMN id_detail_millage SET DEFAULT nextval('public.detail_millages_id_detail_millage_seq'::regclass);


--
-- TOC entry 5055 (class 2604 OID 32783)
-- Name: detail_tugasluars id_detail_tugasluar; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars ALTER COLUMN id_detail_tugasluar SET DEFAULT nextval('public.detail_tugasluars_id_detail_tugasluar_seq'::regclass);


--
-- TOC entry 5058 (class 2604 OID 32784)
-- Name: divisis id_divisi; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.divisis ALTER COLUMN id_divisi SET DEFAULT nextval('public.divisis_id_divisi_seq'::regclass);


--
-- TOC entry 5059 (class 2604 OID 32785)
-- Name: events id_event; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.events ALTER COLUMN id_event SET DEFAULT nextval('public.events_id_event_seq'::regclass);


--
-- TOC entry 5060 (class 2604 OID 32786)
-- Name: export_slip_lemburs id_export_slip_lembur; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.export_slip_lemburs ALTER COLUMN id_export_slip_lembur SET DEFAULT nextval('public.export_slip_lemburs_id_export_slip_lembur_seq'::regclass);


--
-- TOC entry 5062 (class 2604 OID 32787)
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- TOC entry 5064 (class 2604 OID 32788)
-- Name: gaji_departemens id_gaji_departemen; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.gaji_departemens ALTER COLUMN id_gaji_departemen SET DEFAULT nextval('public.gaji_departemens_id_gaji_departemen_seq'::regclass);


--
-- TOC entry 5068 (class 2604 OID 32789)
-- Name: grup_patterns id_grup_pattern; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grup_patterns ALTER COLUMN id_grup_pattern SET DEFAULT nextval('public.grup_patterns_id_grup_pattern_seq'::regclass);


--
-- TOC entry 5069 (class 2604 OID 32790)
-- Name: grups id_grup; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grups ALTER COLUMN id_grup SET DEFAULT nextval('public.grups_id_grup_seq'::regclass);


--
-- TOC entry 5074 (class 2604 OID 32791)
-- Name: jabatans id_jabatan; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jabatans ALTER COLUMN id_jabatan SET DEFAULT nextval('public.jabatans_id_jabatan_seq'::regclass);


--
-- TOC entry 5075 (class 2604 OID 32792)
-- Name: jenis_cutis id_jenis_cuti; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jenis_cutis ALTER COLUMN id_jenis_cuti SET DEFAULT nextval('public.jenis_cutis_id_jenis_cuti_seq'::regclass);


--
-- TOC entry 5078 (class 2604 OID 32793)
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- TOC entry 5079 (class 2604 OID 32794)
-- Name: karyawan_posisi id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawan_posisi ALTER COLUMN id SET DEFAULT nextval('public.karyawan_posisi_id_seq'::regclass);


--
-- TOC entry 5088 (class 2604 OID 32795)
-- Name: ksk_change_histories id_ksk_change_history; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_change_histories ALTER COLUMN id_ksk_change_history SET DEFAULT nextval('public.ksk_change_histories_id_ksk_change_history_seq'::regclass);


--
-- TOC entry 5089 (class 2604 OID 32796)
-- Name: ksk_details id_ksk_detail; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details ALTER COLUMN id_ksk_detail SET DEFAULT nextval('public.ksk_details_id_ksk_detail_seq'::regclass);


--
-- TOC entry 5095 (class 2604 OID 32797)
-- Name: lembur_harians id_lembur_harian; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lembur_harians ALTER COLUMN id_lembur_harian SET DEFAULT nextval('public.lembur_harians_id_lembur_harian_seq'::regclass);


--
-- TOC entry 5102 (class 2604 OID 32798)
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- TOC entry 5104 (class 2604 OID 32799)
-- Name: organisasis id_organisasi; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.organisasis ALTER COLUMN id_organisasi SET DEFAULT nextval('public.organisasis_id_organisasi_seq'::regclass);


--
-- TOC entry 5105 (class 2604 OID 32800)
-- Name: permissions id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.permissions ALTER COLUMN id SET DEFAULT nextval('public.permissions_id_seq'::regclass);


--
-- TOC entry 5106 (class 2604 OID 32801)
-- Name: personal_access_tokens id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.personal_access_tokens ALTER COLUMN id SET DEFAULT nextval('public.personal_access_tokens_id_seq'::regclass);


--
-- TOC entry 5107 (class 2604 OID 32802)
-- Name: pikets id_piket; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.pikets ALTER COLUMN id_piket SET DEFAULT nextval('public.pikets_id_piket_seq'::regclass);


--
-- TOC entry 5108 (class 2604 OID 32803)
-- Name: posisis id_posisi; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.posisis ALTER COLUMN id_posisi SET DEFAULT nextval('public.posisis_id_posisi_seq'::regclass);


--
-- TOC entry 5109 (class 2604 OID 32804)
-- Name: rekap_lembur id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur ALTER COLUMN id SET DEFAULT nextval('public.rekap_lembur_id_seq'::regclass);


--
-- TOC entry 5111 (class 2604 OID 32805)
-- Name: rekap_lembur_summary id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur_summary ALTER COLUMN id SET DEFAULT nextval('public.rekap_lembur_summary_id_seq'::regclass);


--
-- TOC entry 5113 (class 2604 OID 32806)
-- Name: reset_cutis id_reset_cuti; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.reset_cutis ALTER COLUMN id_reset_cuti SET DEFAULT nextval('public.reset_cutis_id_reset_cuti_seq'::regclass);


--
-- TOC entry 5115 (class 2604 OID 32807)
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- TOC entry 5116 (class 2604 OID 32808)
-- Name: sakits id_sakit; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sakits ALTER COLUMN id_sakit SET DEFAULT nextval('public.sakits_id_sakit_seq'::regclass);


--
-- TOC entry 5118 (class 2604 OID 32809)
-- Name: seksis id_seksi; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.seksis ALTER COLUMN id_seksi SET DEFAULT nextval('public.seksis_id_seksi_seq'::regclass);


--
-- TOC entry 5119 (class 2604 OID 32810)
-- Name: setting_lembur_karyawans id_setting_lembur_karyawan; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans ALTER COLUMN id_setting_lembur_karyawan SET DEFAULT nextval('public.setting_lembur_karyawans_id_setting_lembur_karyawan_seq'::regclass);


--
-- TOC entry 5121 (class 2604 OID 32811)
-- Name: setting_lemburs id_setting_lembur; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lemburs ALTER COLUMN id_setting_lembur SET DEFAULT nextval('public.setting_lemburs_id_setting_lembur_seq'::regclass);


--
-- TOC entry 5122 (class 2604 OID 32812)
-- Name: setting_tugasluars id_setting_tugasluar; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_tugasluars ALTER COLUMN id_setting_tugasluar SET DEFAULT nextval('public.setting_tugasluars_id_setting_tugasluar_seq'::regclass);


--
-- TOC entry 5123 (class 2604 OID 32813)
-- Name: slip_lembur_karyawans id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.slip_lembur_karyawans ALTER COLUMN id SET DEFAULT nextval('public.slip_lembur_karyawans_id_seq'::regclass);


--
-- TOC entry 5132 (class 2604 OID 32814)
-- Name: sto_headers id_sto_header; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_headers ALTER COLUMN id_sto_header SET DEFAULT nextval('public.sto_headers_id_sto_header_seq'::regclass);


--
-- TOC entry 5134 (class 2604 OID 32815)
-- Name: sto_lines id_sto_line; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_lines ALTER COLUMN id_sto_line SET DEFAULT nextval('public.sto_lines_id_sto_line_seq'::regclass);


--
-- TOC entry 5136 (class 2604 OID 32816)
-- Name: sto_upload id_sto_upload; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_upload ALTER COLUMN id_sto_upload SET DEFAULT nextval('public.sto_upload_id_sto_upload_seq'::regclass);


--
-- TOC entry 5141 (class 2604 OID 32817)
-- Name: templates id_template; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.templates ALTER COLUMN id_template SET DEFAULT nextval('public.templates_id_template_seq'::regclass);


--
-- TOC entry 5153 (class 2604 OID 32818)
-- Name: turnovers id_turnover; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.turnovers ALTER COLUMN id_turnover SET DEFAULT nextval('public.turnovers_id_turnover_seq'::regclass);


--
-- TOC entry 5154 (class 2604 OID 32819)
-- Name: users id; Type: DEFAULT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- TOC entry 5570 (class 0 OID 16399)
-- Dependencies: 215
-- Data for Name: activity_log; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.activity_log (id, log_name, description, subject_type, subject_id, causer_type, causer_id, properties, created_at, updated_at, event, batch_uuid) FROM stdin;
1	job_upload_karyawan	Upload karyawan - 5 datas	\N	\N	App\\Models\\User	2	[]	2025-09-13 13:57:51	2025-09-13 13:57:51	\N	\N
\.


--
-- TOC entry 5572 (class 0 OID 16405)
-- Dependencies: 217
-- Data for Name: approval_cutis; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.approval_cutis (id_approval_cuti, cuti_id, checked1_for, checked1_by, checked1_karyawan_id, checked2_for, checked2_by, checked2_karyawan_id, approved_for, approved_by, approved_karyawan_id, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5574 (class 0 OID 16411)
-- Dependencies: 219
-- Data for Name: attachment_ksk_details; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.attachment_ksk_details (id_attachment_ksk_detail, ksk_detail_id, path, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5576 (class 0 OID 16415)
-- Dependencies: 221
-- Data for Name: attachment_lemburs; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.attachment_lemburs (id_attachment_lembur, lembur_id, path, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5578 (class 0 OID 16421)
-- Dependencies: 223
-- Data for Name: attendance_devices; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.attendance_devices (id_device, organisasi_id, cloud_id, device_sn, device_name, server_ip, server_port, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5580 (class 0 OID 16427)
-- Dependencies: 225
-- Data for Name: attendance_gps; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.attendance_gps (id_att_gps, karyawan_id, organisasi_id, departemen_id, divisi_id, pin, latitude, longitude, attendance_date, attendance_time, attachment, type, status, scanlog_id, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5582 (class 0 OID 16434)
-- Dependencies: 227
-- Data for Name: attendance_karyawan_grup; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.attendance_karyawan_grup (id, karyawan_id, organisasi_id, grup_id, active_date, toleransi_waktu, jam_masuk, jam_keluar, created_at, updated_at, pin) FROM stdin;
\.


--
-- TOC entry 5584 (class 0 OID 16443)
-- Dependencies: 229
-- Data for Name: attendance_scanlogs; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.attendance_scanlogs (id_scanlog, organisasi_id, device_id, start_date_scan, end_date_scan, scan_date, scan_status, verify, created_at, updated_at, pin) FROM stdin;
\.


--
-- TOC entry 5586 (class 0 OID 16447)
-- Dependencies: 231
-- Data for Name: attendance_summaries; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.attendance_summaries (id_att_summary, karyawan_id, pin, periode, organisasi_id, divisi_id, departemen_id, seksi_id, jabatan_id, total_absen, total_sakit, total_izin, total_hadir, keterlambatan, is_cutoff, tanggal1_selisih, tanggal1_status, tanggal1_in, tanggal1_out, tanggal2_selisih, tanggal2_status, tanggal2_in, tanggal2_out, tanggal3_selisih, tanggal3_status, tanggal3_in, tanggal3_out, tanggal4_selisih, tanggal4_status, tanggal4_in, tanggal4_out, tanggal5_selisih, tanggal5_status, tanggal5_in, tanggal5_out, tanggal6_selisih, tanggal6_status, tanggal6_in, tanggal6_out, tanggal7_selisih, tanggal7_status, tanggal7_in, tanggal7_out, tanggal8_selisih, tanggal8_status, tanggal8_in, tanggal8_out, tanggal9_selisih, tanggal9_status, tanggal9_in, tanggal9_out, tanggal10_selisih, tanggal10_status, tanggal10_in, tanggal10_out, tanggal11_selisih, tanggal11_status, tanggal11_in, tanggal11_out, tanggal12_selisih, tanggal12_status, tanggal12_in, tanggal12_out, tanggal13_selisih, tanggal13_status, tanggal13_in, tanggal13_out, tanggal14_selisih, tanggal14_status, tanggal14_in, tanggal14_out, tanggal15_selisih, tanggal15_status, tanggal15_in, tanggal15_out, tanggal16_selisih, tanggal16_status, tanggal16_in, tanggal16_out, tanggal17_selisih, tanggal17_status, tanggal17_in, tanggal17_out, tanggal18_selisih, tanggal18_status, tanggal18_in, tanggal18_out, tanggal19_selisih, tanggal19_status, tanggal19_in, tanggal19_out, tanggal20_selisih, tanggal20_status, tanggal20_in, tanggal20_out, tanggal21_selisih, tanggal21_status, tanggal21_in, tanggal21_out, tanggal22_selisih, tanggal22_status, tanggal22_in, tanggal22_out, tanggal23_selisih, tanggal23_status, tanggal23_in, tanggal23_out, tanggal24_selisih, tanggal24_status, tanggal24_in, tanggal24_out, tanggal25_selisih, tanggal25_status, tanggal25_in, tanggal25_out, tanggal26_selisih, tanggal26_status, tanggal26_in, tanggal26_out, tanggal27_selisih, tanggal27_status, tanggal27_in, tanggal27_out, tanggal28_selisih, tanggal28_status, tanggal28_in, tanggal28_out, tanggal29_selisih, tanggal29_status, tanggal29_in, tanggal29_out, tanggal30_selisih, tanggal30_status, tanggal30_in, tanggal30_out, tanggal31_selisih, tanggal31_status, tanggal31_in, tanggal31_out, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5588 (class 0 OID 16521)
-- Dependencies: 233
-- Data for Name: cache; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.cache (key, value, expiration) FROM stdin;
\.


--
-- TOC entry 5589 (class 0 OID 16526)
-- Dependencies: 234
-- Data for Name: cache_locks; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.cache_locks (key, owner, expiration) FROM stdin;
\.


--
-- TOC entry 5590 (class 0 OID 16531)
-- Dependencies: 235
-- Data for Name: cleareance_details; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.cleareance_details (id_cleareance_detail, organisasi_id, cleareance_id, type, is_clear, keterangan, confirmed_by_id, confirmed_by, confirmed_at, attachment, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5592 (class 0 OID 16538)
-- Dependencies: 237
-- Data for Name: cleareance_settings; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.cleareance_settings (id_cleareance_setting, organisasi_id, type, karyawan_id, ni_karyawan, nama_karyawan, signature, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5594 (class 0 OID 16544)
-- Dependencies: 239
-- Data for Name: cleareances; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.cleareances (id_cleareance, karyawan_id, organisasi_id, divisi_id, departemen_id, jabatan_id, posisi_id, nama_divisi, nama_departemen, nama_jabatan, nama_posisi, tanggal_akhir_bekerja, status, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5595 (class 0 OID 16550)
-- Dependencies: 240
-- Data for Name: cutis; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.cutis (id_cuti, karyawan_id, organisasi_id, penggunaan_sisa_cuti, jenis_cuti, jenis_cuti_id, durasi_cuti, rencana_mulai_cuti, rencana_selesai_cuti, aktual_mulai_cuti, aktual_selesai_cuti, alasan_cuti, karyawan_pengganti_id, checked1_at, checked1_by, checked2_at, checked2_by, approved_at, approved_by, legalized_at, legalized_by, rejected_at, rejected_by, rejected_note, status_dokumen, status_cuti, attachment, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5597 (class 0 OID 16561)
-- Dependencies: 242
-- Data for Name: departemens; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.departemens (id_departemen, divisi_id, nama, deleted_at, created_at, updated_at) FROM stdin;
3	11	FA & TAX	2025-09-13 11:37:15	2025-09-13 11:36:57	2025-09-13 11:37:15
2	11	MAKETING & SALES	2025-09-13 11:37:18	2025-09-13 11:36:46	2025-09-13 11:37:18
1	4	PRODUKSI 2	2025-09-13 11:37:21	2025-09-13 11:36:32	2025-09-13 11:37:21
4	4	STAMPING	\N	2025-09-13 11:37:45	2025-09-13 11:37:45
5	4	WELDING	\N	2025-09-13 11:37:52	2025-09-13 11:37:52
6	4	PPIC	\N	2025-09-13 11:37:55	2025-09-13 11:37:55
7	4	QUALITY	\N	2025-09-13 11:38:02	2025-09-13 11:38:02
8	7	PLAN SERVICE	\N	2025-09-13 11:38:23	2025-09-13 11:38:23
9	4	MIP	\N	2025-09-13 11:38:34	2025-09-13 11:38:34
10	7	TOOL MAKING	\N	2025-09-13 11:38:42	2025-09-13 11:38:42
11	7	DESIGN & PPF	\N	2025-09-13 11:38:53	2025-09-13 11:38:53
12	11	FA & TAX	\N	2025-09-13 11:39:02	2025-09-13 11:39:02
13	11	HRD & GA	\N	2025-09-13 11:39:11	2025-09-13 11:39:11
14	11	PURCHASING	\N	2025-09-13 11:39:24	2025-09-13 11:39:24
15	11	ICT	\N	2025-09-13 11:39:30	2025-09-13 11:39:30
16	11	MARKETING & SALES	\N	2025-09-13 11:39:37	2025-09-13 11:39:37
17	4	PRODUKSI 2	\N	2025-09-13 11:39:48	2025-09-13 11:39:48
\.


--
-- TOC entry 5599 (class 0 OID 16565)
-- Dependencies: 244
-- Data for Name: detail_lemburs; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.detail_lemburs (id_detail_lembur, lembur_id, karyawan_id, organisasi_id, departemen_id, divisi_id, rencana_mulai_lembur, rencana_selesai_lembur, is_rencana_approved, aktual_mulai_lembur, aktual_selesai_lembur, is_aktual_approved, durasi, deskripsi_pekerjaan, keterangan, nominal, deleted_at, created_at, updated_at, durasi_istirahat, durasi_konversi_lembur, gaji_lembur, uang_makan, pembagi_upah_lembur, rencana_last_changed_by, rencana_last_changed_at, aktual_last_changed_by, aktual_last_changed_at) FROM stdin;
\.


--
-- TOC entry 5601 (class 0 OID 16582)
-- Dependencies: 246
-- Data for Name: detail_millages; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.detail_millages (id_detail_millage, organisasi_id, millage_id, type, attachment, nominal, is_active, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5603 (class 0 OID 16590)
-- Dependencies: 248
-- Data for Name: detail_tugasluars; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.detail_tugasluars (id_detail_tugasluar, tugasluar_id, karyawan_id, organisasi_id, departemen_id, divisi_id, ni_karyawan, pin, date, is_active, role, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5605 (class 0 OID 16598)
-- Dependencies: 250
-- Data for Name: divisis; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.divisis (id_divisi, nama, deleted_at, created_at, updated_at) FROM stdin;
1	ICT	\N	2025-09-13 11:33:32	2025-09-13 11:33:32
2	FA & TAX	\N	2025-09-13 11:34:01	2025-09-13 11:34:01
3	PURCHASING	\N	2025-09-13 11:34:19	2025-09-13 11:34:19
4	STAMPING	\N	2025-09-13 11:34:23	2025-09-13 11:34:23
5	PPIC	\N	2025-09-13 11:34:30	2025-09-13 11:34:30
6	WELDING	\N	2025-09-13 11:34:33	2025-09-13 11:34:33
7	DESIGN & PPF	\N	2025-09-13 11:34:43	2025-09-13 11:34:43
8	PLAN SERVICE	\N	2025-09-13 11:34:51	2025-09-13 11:34:51
9	MIP	\N	2025-09-13 11:34:59	2025-09-13 11:34:59
10	PRODUKSI 2	\N	2025-09-13 11:35:03	2025-09-13 11:35:03
11	HRD & GA	\N	2025-09-13 11:35:10	2025-09-13 11:35:10
12	QUALITY ASSURANCE	\N	2025-09-13 11:35:21	2025-09-13 11:35:21
13	TOOL MAKING	\N	2025-09-13 11:35:32	2025-09-13 11:35:32
14	SALES & MARKETING	\N	2025-09-13 11:35:41	2025-09-13 11:35:41
\.


--
-- TOC entry 5607 (class 0 OID 16602)
-- Dependencies: 252
-- Data for Name: events; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.events (id_event, organisasi_id, jenis_event, keterangan, durasi, tanggal_mulai, tanggal_selesai, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5609 (class 0 OID 16606)
-- Dependencies: 254
-- Data for Name: export_slip_lemburs; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.export_slip_lemburs (id_export_slip_lembur, organisasi_id, departemen_id, periode, status, attachment, message, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5611 (class 0 OID 16613)
-- Dependencies: 256
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- TOC entry 5613 (class 0 OID 16620)
-- Dependencies: 258
-- Data for Name: gaji_departemens; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.gaji_departemens (id_gaji_departemen, departemen_id, periode, total_gaji, nominal_batas_lembur, created_at, updated_at, organisasi_id, presentase) FROM stdin;
\.


--
-- TOC entry 5615 (class 0 OID 16627)
-- Dependencies: 260
-- Data for Name: grup_patterns; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.grup_patterns (id_grup_pattern, organisasi_id, nama, urutan, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5617 (class 0 OID 16633)
-- Dependencies: 262
-- Data for Name: grups; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.grups (id_grup, nama, deleted_at, created_at, updated_at, toleransi_waktu, jam_masuk, jam_keluar) FROM stdin;
\.


--
-- TOC entry 5619 (class 0 OID 16640)
-- Dependencies: 264
-- Data for Name: izins; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.izins (id_izin, karyawan_id, organisasi_id, departemen_id, divisi_id, rencana_mulai_or_masuk, rencana_selesai_or_keluar, aktual_mulai_or_masuk, aktual_selesai_or_keluar, durasi, keterangan, karyawan_pengganti_id, checked_at, checked_by, approved_at, approved_by, legalized_at, legalized_by, rejected_at, rejected_by, rejected_note, created_at, updated_at, jenis_izin) FROM stdin;
\.


--
-- TOC entry 5620 (class 0 OID 16647)
-- Dependencies: 265
-- Data for Name: jabatans; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.jabatans (id_jabatan, nama, deleted_at, created_at, updated_at) FROM stdin;
1	BOD	\N	2025-05-26 10:07:52	2025-05-26 10:07:52
5	LEADER	\N	2025-05-26 10:07:52	2025-05-26 10:07:52
6	STAFF/OPERATOR	\N	2025-05-26 10:07:52	2025-05-26 10:07:52
2	DEPARTEMEN HEAD	\N	2025-05-26 10:07:52	2025-07-30 13:45:51
4	SECTION HEAD	\N	2025-05-26 10:07:52	2025-07-30 14:38:04
3	TIDAK DIPAKAI	\N	2025-05-26 10:07:52	2025-07-30 14:39:09
\.


--
-- TOC entry 5622 (class 0 OID 16651)
-- Dependencies: 267
-- Data for Name: jenis_cutis; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.jenis_cutis (id_jenis_cuti, jenis, durasi, "isUrgent", deleted_at, created_at, updated_at, "isWorkday") FROM stdin;
\.


--
-- TOC entry 5624 (class 0 OID 16661)
-- Dependencies: 269
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- TOC entry 5625 (class 0 OID 16666)
-- Dependencies: 270
-- Data for Name: jobs; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.jobs (id, queue, payload, attempts, reserved_at, available_at, created_at) FROM stdin;
\.


--
-- TOC entry 5627 (class 0 OID 16672)
-- Dependencies: 272
-- Data for Name: karyawan_posisi; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.karyawan_posisi (id, karyawan_id, posisi_id, created_at, updated_at, deleted_at) FROM stdin;
1	ASIPLANT1-WN1757746669109	1	\N	\N	\N
2	ASIPLANT1-FN1757746669803	4	\N	\N	\N
3	ASIPLANT1-SA1757746670457	5	\N	\N	\N
4	ASIPLANT1-AN1757746670498	7	\N	\N	\N
5	ASIPLANT1-CT1757746670239	16	\N	\N	\N
\.


--
-- TOC entry 5629 (class 0 OID 16676)
-- Dependencies: 274
-- Data for Name: karyawans; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.karyawans (id_karyawan, ni_karyawan, user_id, grup_id, organisasi_id, no_kk, nik, nama, tempat_lahir, tanggal_lahir, alamat, domisili, email, no_telp, gol_darah, jenis_kelamin, agama, status_keluarga, kategori_keluarga, npwp, no_bpjs_kt, no_bpjs_ks, jenis_kontrak, status_karyawan, sisa_cuti_pribadi, sisa_cuti_bersama, sisa_cuti_tahun_lalu, expired_date_cuti_tahun_lalu, hutang_cuti, no_rekening, nama_rekening, nama_bank, nama_ibu_kandung, jenjang_pendidikan, jurusan_pendidikan, no_telp_darurat, tanggal_mulai, tanggal_selesai, foto, created_at, updated_at, deleted_at, pin, grup_pattern_id) FROM stdin;
ASIPLANT1-WN1757746669109	3214-0001	4	\N	1	3215052112960002	3215052112960002	WENDI NUGRAHA NURRAHMANSYAH	KARAWANG	1996-12-21	CITRA SWARNA GRANDE CLUSTER KANA K14/16, RT 000 RW 000, DS. PANCAWATI, KEC. KLARI, KAB. KARAWANG, JAWA BARAT 41371	CITRA SWARNA GRANDE CLUSTER KANA K14/16, RT 000 RW 000, DS. PANCAWATI, KEC. KLARI, KAB. KARAWANG, JAWA BARAT 41371	wnnurrahmansyah21@gmail.com	08989815081	B	L	ISLAM	MENIKAH	K2	3215052112960002	9012412112	1402612112	PKWTT	AT	12	0	0	2026-06-01	0	1730006592112	WENDI NUGRAHA NURRAHMANSYAH	MANDIRI	ELA HAYATI	S3	INFORMATIKA	081295524662	2020-06-01	\N	\N	\N	\N	\N	21126	\N
ASIPLANT1-FN1757746669803	3214-0002	5	\N	1	3215050101040001	3215050101040001	FAJAR NUR FARRIJAL	BANDUNG	2004-01-01	JL. PURWAKARTA, RT.001 RW.001, DS. PURWAKARTA, KEC. PURWAKARTA, KAB. PURWAKARTA, JAWABARAT 41111	JL. PURWAKARTA, RT.001 RW.001, DS. PURWAKARTA, KEC. PURWAKARTA, KAB. PURWAKARTA, JAWABARAT 41111	fajarnurfarrijar@gmail.com	0895806317711	A	L	ISLAM	BELUM MENIKAH	TK0	3215050101040001	9012410101	1402610101	PKWTT	AT	12	0	0	2026-06-02	0	1730006590101	FAJR NUR FARRIJAL	MANDIRI	SUMINAH	S2	INFORMATIKA	0895806317711	2025-06-06	\N	\N	\N	\N	\N	11223	\N
ASIPLANT1-SA1757746670457	3214-0003	6	\N	1	3215050202040002	3215050202040002	SALFA ALFARISY	KARAWANG	2004-02-02	JL. PURWAKARTA, RT.001 RW.002, DS. PURWAKARTA, KEC. PURWAKARTA, KAB. PURWAKARTA, JAWABARAT 41111	JL. PURWAKARTA, RT.001 RW.002, DS. PURWAKARTA, KEC. PURWAKARTA, KAB. PURWAKARTA, JAWABARAT 41111	salfaalfarisyi@gmail.com	089518976773	B	L	ISLAM	BELUM MENIKAH	TK0	3215050202040002	9012410202	1402610202	PKWTT	AT	12	0	0	2026-06-03	0	1730006590202	SALFA ALFARISYI	MANDIRI	SURATMI	S2	INFORMATIKA	089518976773	2025-06-06	\N	\N	\N	\N	\N	11224	\N
ASIPLANT1-AN1757746670498	3214-0004	7	\N	1	3215051202030006	3215051202030006	ADHI NUR FAJAR	KARAWANG	2003-02-12	PERUM TERANGSARI E-6/15, RT.001 RW.005, DS. CIBALONGSARI, KEC. KLARI, KAB. KARAWANG, JAWA BARAT 41371	PERUM TERANGSARI E-6/15, RT.001 RW.005, DS. CIBALONGSARI, KEC. KLARI, KAB. KARAWANG, JAWA BARAT 41371	adhinurfajar78@gmail.com	089527897873	B	L	ISLAM	BELUM MENIKAH	TK0	3215051202030006	1234567890	1234567890	PKWTT	AT	12	0	0	2026-01-01	0	1730012020306	ADHI NUR FAJAR	MANDIRI	ELA HAYATI	S2	INFORMATIKA	081382863277	2024-01-01	\N	\N	\N	\N	\N	12023	\N
ASIPLANT1-CT1757746670239	3214-0019	8	\N	1	3215050303040003	3215050303040003	CHRISTOPAN TANGGUH SANTOSA	KARAWANG	2004-03-03	JL. PURWAKARTA, RT.001 RW.003, DS. PURWAKARTA, KEC. PURWAKARTA, KAB. PURWAKARTA, JAWABARAT 41111	JL. PURWAKARTA, RT.001 RW.003, DS. PURWAKARTA, KEC. PURWAKARTA, KAB. PURWAKARTA, JAWABARAT 41111	christopantangguh@gmail.com	085885960903	O	L	ISLAM	BELUM MENIKAH	TK0	3215050303040003	9012410303	1402610303	PKWTT	AT	12	0	0	2026-01-02	0	1730006590303	CHRISTOPAN TANGGUH SANTOSA	MANDIRI	IJAH	S2	INFORMATIKA	085885960903	2025-06-06	\N	\N	\N	\N	\N	11225	\N
\.


--
-- TOC entry 5630 (class 0 OID 16693)
-- Dependencies: 275
-- Data for Name: kontraks; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.kontraks (id_kontrak, karyawan_id, posisi_id, organisasi_id, nama_posisi, no_surat, jenis, status, durasi, salary, deskripsi, tanggal_mulai, tanggal_selesai, tanggal_mulai_before, tanggal_selesai_before, "isReactive", issued_date, tempat_administrasi, status_change_by, status_change_date, attachment, evidence, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5631 (class 0 OID 16704)
-- Dependencies: 276
-- Data for Name: ksk; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.ksk (id_ksk, organisasi_id, divisi_id, nama_divisi, departemen_id, nama_departemen, release_date, parent_id, released_by_id, released_by, released_at, checked_by_id, checked_by, checked_at, approved_by_id, approved_by, approved_at, reviewed_div_by_id, reviewed_div_by, reviewed_div_at, reviewed_ph_by_id, reviewed_ph_by, reviewed_ph_at, reviewed_dir_by_id, reviewed_dir_by, reviewed_dir_at, legalized_by, legalized_at, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5632 (class 0 OID 16710)
-- Dependencies: 277
-- Data for Name: ksk_change_histories; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.ksk_change_histories (id_ksk_change_history, ksk_detail_id, changed_by_id, changed_by, changed_at, reason, status_ksk_before, status_ksk_after, durasi_before, durasi_after, created_at, updated_at, deleted_at, jenis_kontrak_before, jenis_kontrak_after) FROM stdin;
\.


--
-- TOC entry 5634 (class 0 OID 16716)
-- Dependencies: 279
-- Data for Name: ksk_details; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.ksk_details (id_ksk_detail, ksk_id, organisasi_id, divisi_id, nama_divisi, departemen_id, nama_departemen, karyawan_id, ni_karyawan, nama_karyawan, posisi_id, nama_posisi, jabatan_id, nama_jabatan, jenis_kontrak, jumlah_surat_peringatan, jumlah_sakit, jumlah_izin, jumlah_alpa, status_ksk, tanggal_renewal_kontrak, durasi_renewal, cleareance_id, kontrak_id, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5636 (class 0 OID 16727)
-- Dependencies: 281
-- Data for Name: lembur_harians; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.lembur_harians (id_lembur_harian, organisasi_id, departemen_id, divisi_id, total_durasi_lembur, total_nominal_lembur, tanggal_lembur, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5638 (class 0 OID 16733)
-- Dependencies: 283
-- Data for Name: lemburs; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.lemburs (id_lembur, organisasi_id, departemen_id, divisi_id, plan_checked_by, plan_checked_at, plan_approved_by, plan_approved_at, plan_legalized_by, plan_legalized_at, actual_checked_by, actual_checked_at, actual_approved_by, actual_approved_at, actual_legalized_by, actual_legalized_at, total_durasi, status, attachment, issued_date, issued_by, deleted_at, created_at, updated_at, jenis_hari, rejected_by, rejected_at, rejected_note, plan_reviewed_by, plan_reviewed_at, actual_reviewed_by, actual_reviewed_at, total_nominal) FROM stdin;
\.


--
-- TOC entry 5639 (class 0 OID 16744)
-- Dependencies: 284
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.migrations (id, migration, batch) FROM stdin;
\.


--
-- TOC entry 5641 (class 0 OID 16748)
-- Dependencies: 286
-- Data for Name: millages; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.millages (id_millage, karyawan_id, organisasi_id, departemen_id, divisi_id, nama_karyawan, ni_karyawan, no_polisi, is_claimed, checked_by, checked_at, legalized_by, legalized_at, rejected_by, rejected_at, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5642 (class 0 OID 16755)
-- Dependencies: 287
-- Data for Name: model_has_permissions; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.model_has_permissions (permission_id, model_type, model_id) FROM stdin;
\.


--
-- TOC entry 5643 (class 0 OID 16758)
-- Dependencies: 288
-- Data for Name: model_has_roles; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.model_has_roles (role_id, model_type, model_id) FROM stdin;
1	App\\Models\\User	1
2	App\\Models\\User	2
6	App\\Models\\User	3
3	App\\Models\\User	4
3	App\\Models\\User	5
3	App\\Models\\User	6
3	App\\Models\\User	7
3	App\\Models\\User	8
\.


--
-- TOC entry 5644 (class 0 OID 16761)
-- Dependencies: 289
-- Data for Name: organisasis; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.organisasis (id_organisasi, nama, alamat, deleted_at, created_at, updated_at) FROM stdin;
1	ASI PLANT-1	Jl. Surotokunto Jl. Aria Adiarsa No.109, Warungbambu, Kec. Karawang Tim., Karawang, Jawa Barat 41313	\N	2025-09-13 11:31:13	2025-09-13 11:31:13
\.


--
-- TOC entry 5646 (class 0 OID 16767)
-- Dependencies: 291
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- TOC entry 5647 (class 0 OID 16772)
-- Dependencies: 292
-- Data for Name: permissions; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.permissions (id, name, guard_name, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5649 (class 0 OID 16778)
-- Dependencies: 294
-- Data for Name: personal_access_tokens; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.personal_access_tokens (id, tokenable_type, tokenable_id, name, token, abilities, last_used_at, expires_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5651 (class 0 OID 16784)
-- Dependencies: 296
-- Data for Name: pikets; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.pikets (id_piket, karyawan_id, organisasi_id, departemen_id, expired_date, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5653 (class 0 OID 16788)
-- Dependencies: 298
-- Data for Name: posisis; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.posisis (id_posisi, jabatan_id, organisasi_id, divisi_id, departemen_id, seksi_id, nama, parent_id, deleted_at, created_at, updated_at) FROM stdin;
1	1	\N	\N	\N	\N	MANAGING DIRECTOR	0	\N	2025-06-07 10:10:49	2025-06-07 10:10:49
4	1	\N	\N	\N	\N	MANUFACTURING DIRECTOR	0	\N	2025-06-07 10:12:25	2025-06-07 10:12:25
5	1	\N	\N	\N	\N	ENGINEERING DIRECTOR	0	\N	2025-06-07 10:12:47	2025-06-07 10:12:47
7	1	\N	\N	\N	\N	ADMINISTRATION DIRECTOR	0	\N	2025-06-07 10:13:14	2025-06-07 10:13:14
12	2	1	7	\N	\N	PLANT SERVICE AST. MANAGER	4	\N	2025-06-07 11:06:44	2025-07-30 14:58:54
22	4	1	8	8	8	DESIGN & PPF SECT HEAD	15	\N	2025-06-07 16:20:50	2025-06-07 16:20:50
23	4	\N	4	13	13	MARKETING & SALES SECT HEAD	1	\N	2025-06-07 16:21:42	2025-06-07 16:21:42
24	4	\N	4	11	11	PURCHASING TOOL ROOM SECT HEAD	7	\N	2025-06-07 16:22:30	2025-06-07 16:22:30
25	4	\N	4	10	10	HRD & GA SECT HEAD	7	\N	2025-06-07 16:22:57	2025-06-07 16:22:57
34	5	1	8	8	8	LEADER DESIGN & PPF	22	\N	2025-06-07 16:28:48	2025-06-07 16:28:48
35	5	\N	4	13	13	LEADER MARKETING & SALES	23	\N	2025-06-07 16:29:21	2025-06-07 16:29:21
36	5	1	4	10	10	LEADER HRD & GA	25	\N	2025-06-07 16:30:34	2025-06-07 16:30:34
37	5	1	4	10	10	LEADER SECURITY	25	\N	2025-06-07 16:30:59	2025-06-07 16:30:59
48	6	\N	8	8	8	DESIGN & PPF	34	\N	2025-06-09 09:06:01	2025-06-09 09:06:01
49	6	\N	4	13	13	MARKETING & SALES	35	\N	2025-06-09 09:07:01	2025-06-09 09:07:01
51	6	\N	4	9	9	FA & TAX	16	\N	2025-06-09 09:11:39	2025-06-09 09:11:39
53	6	1	4	10	10	HRD & GA	36	\N	2025-06-09 09:14:48	2025-06-09 09:14:48
54	6	1	4	10	10	SECURITY	37	\N	2025-06-09 09:15:22	2025-06-09 09:15:22
45	6	1	7	5	5	PLANT SERVICE	31	2025-06-11 06:28:54	2025-06-09 08:39:26	2025-06-11 06:28:54
46	6	1	9	6	6	MIP	32	2025-06-11 06:28:57	2025-06-09 08:40:13	2025-06-11 06:28:57
11	2	1	3	\N	\N	QUALITY MANAGER	4	\N	2025-06-07 11:05:39	2025-07-30 14:57:44
10	2	1	10	\N	\N	PPIC MANAGER	4	\N	2025-06-07 11:04:49	2025-07-30 15:00:10
63	4	2	8	8	8	P. ENGINEERING SECT HEAD	56	\N	2025-06-12 17:01:54	2025-06-12 17:01:54
75	6	2	8	8	8	P. ENGINEERING	63	\N	2025-06-13 12:53:38	2025-06-13 12:53:38
13	2	1	6	\N	\N	MIP AST. MANAGER	4	\N	2025-06-07 11:08:51	2025-07-30 15:00:28
77	6	2	4	10	10	HRD & GA	36	\N	2025-06-13 12:55:57	2025-06-13 15:05:25
20	4	1	11	14	14	PRODUKSI 2 SECT HEAD	4	\N	2025-06-07 16:19:05	2025-06-16 13:54:39
42	6	1	11	14	14	PRODUKSI 2	30	\N	2025-06-07 16:33:24	2025-06-16 13:54:39
55	2	2	11	\N	\N	STAMPING AST. MANAGER	4	\N	2025-06-12 16:55:13	2025-07-30 15:04:59
21	4	1	8	7	7	TOOL MAKING SECT HEAD	14	\N	2025-06-07 16:19:45	2025-06-16 13:56:08
33	5	1	8	7	7	LEADER TOOL MAKING	21	\N	2025-06-07 16:28:03	2025-06-16 13:56:08
57	2	2	10	\N	\N	PPIC AST. MANAGER	4	\N	2025-06-12 16:56:50	2025-07-30 15:00:55
32	5	1	11	6	6	LEADER MIP	13	\N	2025-06-07 16:27:27	2025-06-16 14:07:36
44	6	1	11	6	6	MIP	32	\N	2025-06-07 16:43:40	2025-06-16 14:07:36
62	4	2	11	6	6	MIP SECT HEAD	4	\N	2025-06-12 17:00:15	2025-06-16 14:07:36
52	6	\N	4	12	12	ICT	86	\N	2025-06-09 09:14:12	2025-07-30 09:18:13
31	5	1	8	5	5	LEADER PLANT SERVICE	12	\N	2025-06-07 16:26:55	2025-06-16 14:07:49
43	6	1	8	5	5	PLANT SERVICE	31	\N	2025-06-07 16:40:59	2025-06-16 14:07:49
64	4	2	8	5	5	PLANT SERVICE SECT HEAD	4	\N	2025-06-12 17:02:30	2025-06-16 14:07:49
16	2	\N	13	\N	\N	FA & TAX MANAGER	7	\N	2025-06-07 11:11:13	2025-07-30 15:05:23
9	2	1	9	\N	\N	WELDING MANAGER	4	\N	2025-06-07 11:03:53	2025-07-30 15:01:55
29	5	1	11	4	4	LEADER QUALITY	11	\N	2025-06-07 16:25:26	2025-06-16 19:02:08
41	6	1	11	4	4	QUALITY ASSURANCE	29	\N	2025-06-07 16:32:51	2025-06-16 19:02:08
72	6	2	11	4	4	QUALITY ASSURANCE	61	\N	2025-06-13 12:51:44	2025-06-16 19:02:08
79	5	3	11	4	4	LEADER QUALITY	61	\N	2025-06-13 12:58:34	2025-06-16 19:02:08
83	6	3	11	4	4	QUALITY ASSURANCE	79	\N	2025-06-13 13:00:05	2025-06-16 19:02:08
61	4	2	11	4	4	QUALITY SECT HEAD	56	\N	2025-06-12 16:59:21	2025-06-16 19:02:08
19	4	1	11	3	3	PPIC SECT HEAD	10	\N	2025-06-07 16:15:31	2025-06-16 19:02:18
28	5	1	11	3	3	LEADER PPIC	19	\N	2025-06-07 16:24:35	2025-06-16 19:02:18
40	6	1	11	3	3	PPIC	28	\N	2025-06-07 16:32:09	2025-06-16 19:02:18
56	2	2	9	\N	\N	WELDING MANAGER	4	\N	2025-06-12 16:56:03	2025-07-30 15:02:21
60	4	2	11	3	3	PPIC SECT HEAD	57	\N	2025-06-12 16:58:44	2025-06-16 19:02:18
67	5	2	11	3	3	LEADER PPIC	60	\N	2025-06-12 17:05:41	2025-06-16 19:02:18
71	6	2	11	3	3	PPIC	67	\N	2025-06-13 12:51:08	2025-06-16 19:02:18
8	2	1	11	\N	\N	STAMPING AST. MANAGER	4	\N	2025-06-07 11:03:26	2025-07-30 15:03:10
18	4	1	11	2	2	WELDING SECT HEAD	9	\N	2025-06-07 16:14:45	2025-06-16 19:02:51
27	5	1	11	2	2	LEADER WELDING	18	\N	2025-06-07 16:24:03	2025-06-16 19:02:51
39	6	1	11	2	2	WELDING	27	\N	2025-06-07 16:31:50	2025-06-16 19:02:51
14	2	1	2	\N	\N	TOOL MAKING AST. MANAGER	5	\N	2025-06-07 11:09:45	2025-07-30 15:03:37
59	4	2	11	2	2	WELDING SECT HEAD	56	\N	2025-06-12 16:58:15	2025-06-16 19:02:51
66	5	2	11	2	2	LEADER WELDING	59	\N	2025-06-12 17:05:17	2025-06-16 19:02:51
70	6	2	11	2	2	WELDING	66	\N	2025-06-13 12:50:47	2025-06-16 19:02:51
78	5	3	11	2	2	LEADER WELDING	59	\N	2025-06-13 12:58:12	2025-06-16 19:02:51
15	2	1	8	\N	\N	DESIGN & PPF MANAGER	5	\N	2025-06-07 11:10:40	2025-07-30 15:03:59
17	4	1	11	1	1	STAMPING SECT HEAD	8	\N	2025-06-07 16:14:08	2025-06-16 19:03:01
26	5	1	11	1	1	LEADER STAMPING	17	\N	2025-06-07 16:23:29	2025-06-16 19:03:01
38	6	1	11	1	1	STAMPING	26	\N	2025-06-07 16:31:29	2025-06-16 19:03:01
58	4	2	11	1	1	STAMPING SECT HEAD	55	\N	2025-06-12 16:57:38	2025-06-16 19:03:01
65	5	2	11	1	1	LEADER STAMPING	58	\N	2025-06-12 17:04:43	2025-06-16 19:03:01
69	6	2	11	1	1	STAMPING	65	\N	2025-06-13 12:50:12	2025-06-16 19:03:01
30	5	1	11	14	14	LEADER PRODUKSI 2	4	\N	2025-06-07 16:25:59	2025-06-16 13:54:39
47	6	1	8	7	7	TOOL MAKING	33	\N	2025-06-09 09:05:11	2025-06-16 13:56:08
74	6	2	11	6	6	MIP	62	\N	2025-06-13 12:52:33	2025-06-16 14:07:36
68	5	2	8	5	5	LEADER PLANT SERVICE	64	\N	2025-06-12 17:06:30	2025-06-16 14:07:49
73	6	2	8	5	5	PLANT SERVICE	68	\N	2025-06-13 12:52:10	2025-06-16 14:07:49
84	6	3	8	5	5	PLANT SERVICE	80	\N	2025-06-13 13:00:29	2025-06-16 14:07:49
82	6	3	11	3	3	PPIC	67	\N	2025-06-13 12:59:46	2025-06-16 19:02:18
81	6	3	11	2	2	WELDING	78	\N	2025-06-13 12:59:18	2025-06-16 19:02:51
85	2	1	8	\N	\N	ADVISOR	1	\N	2025-06-16 19:07:16	2025-06-16 19:07:16
80	5	3	8	5	5	LEADER PLANT SERVICE	64	\N	2025-06-13 12:58:56	2025-06-17 20:47:32
86	5	\N	4	12	12	LEADER ICT	87	\N	2025-07-30 09:17:51	2025-07-30 10:38:47
87	2	\N	4	\N	\N	DEPT HEAD ICT	7	\N	2025-07-30 10:38:34	2025-07-30 13:48:20
76	6	\N	4	11	11	PURCHASING	50	\N	2025-06-13 12:55:38	2025-08-18 15:07:11
50	5	\N	4	11	11	PURCHASING	7	\N	2025-06-09 09:11:01	2025-06-09 09:11:01
88	5	\N	4	11	11	LEADER PURCHASING	7	2025-08-18 15:06:39	2025-08-18 15:06:10	2025-08-18 15:06:39
\.


--
-- TOC entry 5655 (class 0 OID 16792)
-- Dependencies: 300
-- Data for Name: rekap_lembur; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.rekap_lembur (id, karyawan_id, organisasi_id, departemen, jabatan, periode, gaji_pokok, upah_lembur_per_jam, total_jam_lembur, konversi_jam_lembur, gaji_lembur, uang_makan, total_gaji_lembur, is_locked, created_at, updated_at, pph_persen, total_pph, total_diterima) FROM stdin;
\.


--
-- TOC entry 5657 (class 0 OID 16799)
-- Dependencies: 302
-- Data for Name: rekap_lembur_summary; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.rekap_lembur_summary (id, organisasi_id, departemen, periode, jumlah_karyawan, total_jam_lembur, konversi_jam_lembur, gaji_lembur, uang_makan, total_gaji_lembur, created_at, updated_at, is_locked, pph_persen, total_pph, total_gaji_lembur_diterima) FROM stdin;
\.


--
-- TOC entry 5659 (class 0 OID 16806)
-- Dependencies: 304
-- Data for Name: reset_cutis; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.reset_cutis (id_reset_cuti, reset_at, deleted_at, created_at, updated_at, reset_count) FROM stdin;
\.


--
-- TOC entry 5661 (class 0 OID 16811)
-- Dependencies: 306
-- Data for Name: role_has_permissions; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.role_has_permissions (permission_id, role_id) FROM stdin;
\.


--
-- TOC entry 5662 (class 0 OID 16814)
-- Dependencies: 307
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.roles (id, name, guard_name, created_at, updated_at) FROM stdin;
1	super user	web	2025-09-13 11:11:19	2025-09-13 11:11:19
2	personalia	web	2025-09-13 11:11:19	2025-09-13 11:11:19
3	atasan	web	2025-09-13 11:11:19	2025-09-13 11:11:19
4	member	web	2025-09-13 11:11:19	2025-09-13 11:11:19
5	admin-dept	web	2025-09-13 11:11:19	2025-09-13 11:11:19
6	security	web	2025-09-13 11:11:19	2025-09-13 11:11:19
7	personalia-lembur	web	2025-09-13 11:11:19	2025-09-13 11:11:19
8	super-personalia	web	2025-09-13 11:11:19	2025-09-13 11:11:19
\.


--
-- TOC entry 5664 (class 0 OID 16820)
-- Dependencies: 309
-- Data for Name: sakits; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.sakits (id_sakit, karyawan_id, organisasi_id, departemen_id, divisi_id, tanggal_mulai, tanggal_selesai, durasi, keterangan, karyawan_pengganti_id, approved_at, approved_by, legalized_at, legalized_by, rejected_at, rejected_by, rejected_note, attachment, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5666 (class 0 OID 16827)
-- Dependencies: 311
-- Data for Name: seksis; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.seksis (id_seksi, departemen_id, nama, deleted_at, created_at, updated_at) FROM stdin;
1	4	STAMPING	\N	2025-09-13 11:40:34	2025-09-13 11:40:34
2	5	WELDING	\N	2025-09-13 11:40:45	2025-09-13 11:40:45
3	6	PPIC	\N	2025-09-13 11:40:53	2025-09-13 11:40:53
4	7	QUALITY	\N	2025-09-13 11:41:00	2025-09-13 11:41:00
5	8	PLAN SERVICE	\N	2025-09-13 11:41:10	2025-09-13 11:41:10
6	9	MIP	\N	2025-09-13 11:41:16	2025-09-13 11:41:16
7	10	TOOL MAKING	\N	2025-09-13 11:41:25	2025-09-13 11:41:25
8	11	DESIGN & PPF	\N	2025-09-13 11:41:34	2025-09-13 11:41:34
9	12	FA & TAX	\N	2025-09-13 11:41:43	2025-09-13 11:41:43
10	13	HRD & GA	\N	2025-09-13 11:41:53	2025-09-13 11:41:53
11	14	PURCHASING	\N	2025-09-13 11:42:00	2025-09-13 11:42:00
12	15	ICT	\N	2025-09-13 11:42:06	2025-09-13 11:42:06
13	16	MARKETING & SALES	\N	2025-09-13 11:42:15	2025-09-13 11:42:15
14	17	PRODUKSI 2	\N	2025-09-13 11:42:22	2025-09-13 11:42:22
\.


--
-- TOC entry 5668 (class 0 OID 16831)
-- Dependencies: 313
-- Data for Name: sessions; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.sessions (id, user_id, ip_address, user_agent, payload, last_activity) FROM stdin;
cxuQmSgnbzjbWGG8Je7Xv6Sni3OWrkUqOGzJczEz	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0	YTozOntzOjY6Il90b2tlbiI7czo0MDoid2VBd0JORkdYSkhINmNEbDRTMXhXbXdxVmVKNjFoNDhRclU0bHVxdSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=	1757760592
V0kbbRIOwiiod2ENnscfpbQLkWjrYjRQe3OmdQMm	4	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0	YTo1OntzOjY6Il90b2tlbiI7czo0MDoibnlQSFB5Q2RMNUJvVkgwVGdtQlNTYlFZeHZvSG9nZ2paaU5VdTZMSiI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjY6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDtzOjQ6ImF1dGgiO2E6MTp7czoyMToicGFzc3dvcmRfY29uZmlybWVkX2F0IjtpOjE3NTc3NjA0MTQ7fX0=	1757761035
Xs5j40tnrDBLuQOoLE0ehhJAW5wu3LRhARuLqvP0	\N	127.0.0.1	Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:142.0) Gecko/20100101 Firefox/142.0	YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZHlwRGFtczROaTNDQlNDMW12dVRNWXFvUlpHdW9LVG5sZ1BEdk1BaSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2hvbWUiO31zOjk6Il9wcmV2aW91cyI7YToxOntzOjM6InVybCI7czoyNjoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL2hvbWUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19	1757760592
\.


--
-- TOC entry 5669 (class 0 OID 16836)
-- Dependencies: 314
-- Data for Name: setting_lembur_karyawans; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.setting_lembur_karyawans (id_setting_lembur_karyawan, karyawan_id, organisasi_id, jabatan_id, departemen_id, gaji, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5671 (class 0 OID 16841)
-- Dependencies: 316
-- Data for Name: setting_lemburs; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.setting_lemburs (id_setting_lembur, organisasi_id, setting_name, value, created_at, updated_at, deleted_at) FROM stdin;
1	1	batas_pengajuan_lembur	17:00	\N	\N	\N
2	1	batas_approval_lembur	23:59	\N	\N	\N
3	1	onoff_batas_pengajuan_lembur	Y	\N	\N	\N
4	1	uang_makan	15000	\N	\N	\N
5	1	pembagi_upah_lembur_harian	173	\N	\N	\N
6	1	jam_istirahat_mulai_1	12:00	\N	\N	\N
7	1	jam_istirahat_selesai_1	12:45	\N	\N	\N
8	1	jam_istirahat_mulai_2	18:00	\N	\N	\N
9	1	jam_istirahat_selesai_2	18:45	\N	\N	\N
10	1	jam_istirahat_mulai_3	02:30	\N	\N	\N
11	1	jam_istirahat_selesai_3	03:15	\N	\N	\N
12	1	jam_istirahat_mulai_jumat	11:30	\N	\N	\N
13	1	jam_istirahat_selesai_jumat	13:00	\N	\N	\N
14	1	durasi_istirahat_1	45	\N	\N	\N
15	1	durasi_istirahat_2	45	\N	\N	\N
16	1	durasi_istirahat_3	45	\N	\N	\N
17	1	durasi_istirahat_jumat	90	\N	\N	\N
18	1	insentif_section_head_1	32500	\N	\N	\N
19	1	insentif_section_head_2	67500	\N	\N	\N
20	1	insentif_section_head_3	107500	\N	\N	\N
21	1	insentif_section_head_4	250000	\N	\N	\N
22	1	insentif_department_head_4	400000	\N	\N	\N
\.


--
-- TOC entry 5673 (class 0 OID 16847)
-- Dependencies: 318
-- Data for Name: setting_tugasluars; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.setting_tugasluars (id_setting_tugasluar, organisasi_id, name, value, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5675 (class 0 OID 16853)
-- Dependencies: 320
-- Data for Name: slip_lembur_karyawans; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.slip_lembur_karyawans (id, karyawan_id, organisasi_id, periode, total_lembur, total_uang_makan, total_jam, total_konversi_jam, pph_persen, total_pph, total_diterima, is_locked, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5677 (class 0 OID 16865)
-- Dependencies: 322
-- Data for Name: sto_headers; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.sto_headers (id_sto_header, year, issued_by, issued_name, organization_id, wh_id, wh_name, doc_date, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5679 (class 0 OID 16872)
-- Dependencies: 324
-- Data for Name: sto_lines; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.sto_lines (id_sto_line, inputed_by, inputed_name, updated_by, updated_name, sto_header_id, customer_id, customer_name, location_area, wh_id, wh_name, locator_id, locator_value, no_label, spec_size, product_id, part_code, part_name, part_desc, model, identitas_lot, quantity, status, processed, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5681 (class 0 OID 16879)
-- Dependencies: 326
-- Data for Name: sto_upload; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.sto_upload (id_sto_upload, wh_id, wh_name, locator_id, locator_name, customer_id, customer_name, product_id, product_code, product_name, product_desc, model, qty_book, qty_count, balance, doc_date, processed, created_at, updated_at, organization_id) FROM stdin;
\.


--
-- TOC entry 5683 (class 0 OID 16889)
-- Dependencies: 328
-- Data for Name: templates; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.templates (id_template, organisasi_id, nama, type, template_path, "isActive", deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5685 (class 0 OID 16897)
-- Dependencies: 330
-- Data for Name: tugasluars; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.tugasluars (id_tugasluar, organisasi_id, karyawan_id, ni_karyawan, divisi_id, departemen_id, tanggal, tanggal_pergi_planning, tanggal_kembali_planning, tanggal_pergi_aktual, tanggal_kembali_aktual, jenis_kendaraan, jenis_kepemilikan, jenis_keberangkatan, no_polisi, km_awal, km_akhir, km_selisih, km_standar, pengemudi_id, tempat_asal, tempat_tujuan, keterangan, pembagi, bbm, rate, nominal, millage_id, status, checked_by, checked_at, legalized_by, legalized_at, rejected_by, rejected_at, rejected_note, last_changed_by, last_changed_at, deleted_at, created_at, updated_at) FROM stdin;
\.


--
-- TOC entry 5686 (class 0 OID 16912)
-- Dependencies: 331
-- Data for Name: turnovers; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.turnovers (id_turnover, karyawan_id, organisasi_id, status_karyawan, tanggal_keluar, keterangan, jumlah_aktif_karyawan_terakhir, created_at, updated_at, deleted_at) FROM stdin;
\.


--
-- TOC entry 5688 (class 0 OID 16919)
-- Dependencies: 333
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: ict
--

COPY public.users (id, username, email, organisasi_id, email_verified_at, password, remember_token, created_at, updated_at) FROM stdin;
1	SUPERUSER	wnnurrahmansyah21@gmail.com	\N	\N	$2y$12$LblaBtwLc2FesLBzp7nHhuh/jbeNRyy41nYwrGoKFDdsqBkOe0Ahu	\N	2025-09-13 11:04:28	2025-09-13 11:04:28
2	HRGA	adhinurfajar78@gmail.com	1	\N	$2y$12$8/xJIr140v51oVB5avci5uDUafRNZ87Fv5zbR.yUvAJ.4R7t3AZy2	\N	2025-09-13 11:31:14	2025-09-13 11:31:14
3	SECURITY	wnnurrahmansyah@gmail.com	1	\N	$2y$12$F3AuEtVEOqbOY7EFrM.f.eKot.3gaXtWAexuLtTbMSiXsMPs6grY6	\N	2025-09-13 11:31:14	2025-09-13 11:31:14
4	3214-0001	wendi.nurrahmansyah.team@cybernova.co.id	1	\N	$2y$12$pLV2szxiLVZBwY9ykULDCudG4Nhi0mw/0ri7136G/CV3JDnUJpoyi	\N	\N	\N
5	3214-0002	fajar.farrijal.team@cybernova.co.id	1	\N	$2y$12$P1Yx1q8DinwXlv5297/9rO0UTEc.6PvfmA60uMjb.Ru5XNPGzeyIa	\N	\N	\N
6	3214-0003	salfa.alfarisyi.team@cybernova.co.id	1	\N	$2y$12$Ha7QLyN3I5HCzSi3N2F2H.AiOQjSQIv.mFps/pUmqngAPLolD1ovm	\N	\N	\N
7	3214-0004	adhi.fajar.team@cybernova.co.id	1	\N	$2y$12$lIpMVu.m1PW4qii.1qE70OoKfD9B0XDKbMx3zBEqIJuRXJXgIb5b6	\N	\N	\N
8	3214-0019	christopan.santosa.team@cybernova.co.id	1	\N	$2y$12$6V5QvvGrYM/7.EQhfypbN.cCWbzzsJLePo5k/PBnRCuoA3eb0Ru6.	\N	\N	\N
\.


--
-- TOC entry 5747 (class 0 OID 0)
-- Dependencies: 216
-- Name: activity_log_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.activity_log_id_seq', 1, true);


--
-- TOC entry 5748 (class 0 OID 0)
-- Dependencies: 218
-- Name: approval_cutis_id_approval_cuti_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.approval_cutis_id_approval_cuti_seq', 1, false);


--
-- TOC entry 5749 (class 0 OID 0)
-- Dependencies: 220
-- Name: attachment_ksk_details_id_attachment_ksk_detail_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.attachment_ksk_details_id_attachment_ksk_detail_seq', 1, false);


--
-- TOC entry 5750 (class 0 OID 0)
-- Dependencies: 222
-- Name: attachment_lemburs_id_attachment_lembur_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.attachment_lemburs_id_attachment_lembur_seq', 1, false);


--
-- TOC entry 5751 (class 0 OID 0)
-- Dependencies: 224
-- Name: attendance_devices_id_device_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.attendance_devices_id_device_seq', 1, false);


--
-- TOC entry 5752 (class 0 OID 0)
-- Dependencies: 226
-- Name: attendance_gps_id_att_gps_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.attendance_gps_id_att_gps_seq', 1, false);


--
-- TOC entry 5753 (class 0 OID 0)
-- Dependencies: 228
-- Name: attendance_karyawan_grup_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.attendance_karyawan_grup_id_seq', 1, false);


--
-- TOC entry 5754 (class 0 OID 0)
-- Dependencies: 230
-- Name: attendance_scanlogs_id_scanlog_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.attendance_scanlogs_id_scanlog_seq', 1, false);


--
-- TOC entry 5755 (class 0 OID 0)
-- Dependencies: 232
-- Name: attendance_summaries_id_att_summary_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.attendance_summaries_id_att_summary_seq', 1, false);


--
-- TOC entry 5756 (class 0 OID 0)
-- Dependencies: 236
-- Name: cleareance_details_id_cleareance_detail_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.cleareance_details_id_cleareance_detail_seq', 1, false);


--
-- TOC entry 5757 (class 0 OID 0)
-- Dependencies: 238
-- Name: cleareance_settings_id_cleareance_setting_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.cleareance_settings_id_cleareance_setting_seq', 1, false);


--
-- TOC entry 5758 (class 0 OID 0)
-- Dependencies: 241
-- Name: cutis_id_cuti_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.cutis_id_cuti_seq', 1, false);


--
-- TOC entry 5759 (class 0 OID 0)
-- Dependencies: 243
-- Name: departemens_id_departemen_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.departemens_id_departemen_seq', 17, true);


--
-- TOC entry 5760 (class 0 OID 0)
-- Dependencies: 245
-- Name: detail_lemburs_id_detail_lembur_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.detail_lemburs_id_detail_lembur_seq', 1, false);


--
-- TOC entry 5761 (class 0 OID 0)
-- Dependencies: 247
-- Name: detail_millages_id_detail_millage_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.detail_millages_id_detail_millage_seq', 1, false);


--
-- TOC entry 5762 (class 0 OID 0)
-- Dependencies: 249
-- Name: detail_tugasluars_id_detail_tugasluar_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.detail_tugasluars_id_detail_tugasluar_seq', 1, false);


--
-- TOC entry 5763 (class 0 OID 0)
-- Dependencies: 251
-- Name: divisis_id_divisi_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.divisis_id_divisi_seq', 14, true);


--
-- TOC entry 5764 (class 0 OID 0)
-- Dependencies: 253
-- Name: events_id_event_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.events_id_event_seq', 1, false);


--
-- TOC entry 5765 (class 0 OID 0)
-- Dependencies: 255
-- Name: export_slip_lemburs_id_export_slip_lembur_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.export_slip_lemburs_id_export_slip_lembur_seq', 1, false);


--
-- TOC entry 5766 (class 0 OID 0)
-- Dependencies: 257
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- TOC entry 5767 (class 0 OID 0)
-- Dependencies: 259
-- Name: gaji_departemens_id_gaji_departemen_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.gaji_departemens_id_gaji_departemen_seq', 1, false);


--
-- TOC entry 5768 (class 0 OID 0)
-- Dependencies: 261
-- Name: grup_patterns_id_grup_pattern_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.grup_patterns_id_grup_pattern_seq', 1, false);


--
-- TOC entry 5769 (class 0 OID 0)
-- Dependencies: 263
-- Name: grups_id_grup_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.grups_id_grup_seq', 1, false);


--
-- TOC entry 5770 (class 0 OID 0)
-- Dependencies: 266
-- Name: jabatans_id_jabatan_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.jabatans_id_jabatan_seq', 1, false);


--
-- TOC entry 5771 (class 0 OID 0)
-- Dependencies: 268
-- Name: jenis_cutis_id_jenis_cuti_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.jenis_cutis_id_jenis_cuti_seq', 1, false);


--
-- TOC entry 5772 (class 0 OID 0)
-- Dependencies: 271
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, true);


--
-- TOC entry 5773 (class 0 OID 0)
-- Dependencies: 273
-- Name: karyawan_posisi_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.karyawan_posisi_id_seq', 5, true);


--
-- TOC entry 5774 (class 0 OID 0)
-- Dependencies: 278
-- Name: ksk_change_histories_id_ksk_change_history_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.ksk_change_histories_id_ksk_change_history_seq', 1, false);


--
-- TOC entry 5775 (class 0 OID 0)
-- Dependencies: 280
-- Name: ksk_details_id_ksk_detail_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.ksk_details_id_ksk_detail_seq', 1, false);


--
-- TOC entry 5776 (class 0 OID 0)
-- Dependencies: 282
-- Name: lembur_harians_id_lembur_harian_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.lembur_harians_id_lembur_harian_seq', 1, false);


--
-- TOC entry 5777 (class 0 OID 0)
-- Dependencies: 285
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.migrations_id_seq', 1, false);


--
-- TOC entry 5778 (class 0 OID 0)
-- Dependencies: 290
-- Name: organisasis_id_organisasi_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.organisasis_id_organisasi_seq', 1, true);


--
-- TOC entry 5779 (class 0 OID 0)
-- Dependencies: 293
-- Name: permissions_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.permissions_id_seq', 1, false);


--
-- TOC entry 5780 (class 0 OID 0)
-- Dependencies: 295
-- Name: personal_access_tokens_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.personal_access_tokens_id_seq', 1, false);


--
-- TOC entry 5781 (class 0 OID 0)
-- Dependencies: 297
-- Name: pikets_id_piket_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.pikets_id_piket_seq', 1, false);


--
-- TOC entry 5782 (class 0 OID 0)
-- Dependencies: 299
-- Name: posisis_id_posisi_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.posisis_id_posisi_seq', 4, true);


--
-- TOC entry 5783 (class 0 OID 0)
-- Dependencies: 301
-- Name: rekap_lembur_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.rekap_lembur_id_seq', 1, false);


--
-- TOC entry 5784 (class 0 OID 0)
-- Dependencies: 303
-- Name: rekap_lembur_summary_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.rekap_lembur_summary_id_seq', 1, false);


--
-- TOC entry 5785 (class 0 OID 0)
-- Dependencies: 305
-- Name: reset_cutis_id_reset_cuti_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.reset_cutis_id_reset_cuti_seq', 1, false);


--
-- TOC entry 5786 (class 0 OID 0)
-- Dependencies: 308
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.roles_id_seq', 8, true);


--
-- TOC entry 5787 (class 0 OID 0)
-- Dependencies: 310
-- Name: sakits_id_sakit_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.sakits_id_sakit_seq', 1, false);


--
-- TOC entry 5788 (class 0 OID 0)
-- Dependencies: 312
-- Name: seksis_id_seksi_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.seksis_id_seksi_seq', 14, true);


--
-- TOC entry 5789 (class 0 OID 0)
-- Dependencies: 315
-- Name: setting_lembur_karyawans_id_setting_lembur_karyawan_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.setting_lembur_karyawans_id_setting_lembur_karyawan_seq', 1, false);


--
-- TOC entry 5790 (class 0 OID 0)
-- Dependencies: 317
-- Name: setting_lemburs_id_setting_lembur_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.setting_lemburs_id_setting_lembur_seq', 22, true);


--
-- TOC entry 5791 (class 0 OID 0)
-- Dependencies: 319
-- Name: setting_tugasluars_id_setting_tugasluar_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.setting_tugasluars_id_setting_tugasluar_seq', 1, false);


--
-- TOC entry 5792 (class 0 OID 0)
-- Dependencies: 321
-- Name: slip_lembur_karyawans_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.slip_lembur_karyawans_id_seq', 1, false);


--
-- TOC entry 5793 (class 0 OID 0)
-- Dependencies: 323
-- Name: sto_headers_id_sto_header_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.sto_headers_id_sto_header_seq', 1, false);


--
-- TOC entry 5794 (class 0 OID 0)
-- Dependencies: 325
-- Name: sto_lines_id_sto_line_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.sto_lines_id_sto_line_seq', 1, false);


--
-- TOC entry 5795 (class 0 OID 0)
-- Dependencies: 327
-- Name: sto_upload_id_sto_upload_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.sto_upload_id_sto_upload_seq', 1, false);


--
-- TOC entry 5796 (class 0 OID 0)
-- Dependencies: 329
-- Name: templates_id_template_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.templates_id_template_seq', 1, false);


--
-- TOC entry 5797 (class 0 OID 0)
-- Dependencies: 332
-- Name: turnovers_id_turnover_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.turnovers_id_turnover_seq', 1, false);


--
-- TOC entry 5798 (class 0 OID 0)
-- Dependencies: 334
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: ict
--

SELECT pg_catalog.setval('public.users_id_seq', 8, true);


--
-- TOC entry 5181 (class 2606 OID 16978)
-- Name: activity_log activity_log_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.activity_log
    ADD CONSTRAINT activity_log_pkey PRIMARY KEY (id);


--
-- TOC entry 5185 (class 2606 OID 16980)
-- Name: approval_cutis approval_cutis_cuti_id_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.approval_cutis
    ADD CONSTRAINT approval_cutis_cuti_id_unique UNIQUE (cuti_id);


--
-- TOC entry 5187 (class 2606 OID 16982)
-- Name: approval_cutis approval_cutis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.approval_cutis
    ADD CONSTRAINT approval_cutis_pkey PRIMARY KEY (id_approval_cuti);


--
-- TOC entry 5189 (class 2606 OID 16984)
-- Name: attachment_ksk_details attachment_ksk_details_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_ksk_details
    ADD CONSTRAINT attachment_ksk_details_pkey PRIMARY KEY (id_attachment_ksk_detail);


--
-- TOC entry 5191 (class 2606 OID 16986)
-- Name: attachment_lemburs attachment_lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_lemburs
    ADD CONSTRAINT attachment_lemburs_pkey PRIMARY KEY (id_attachment_lembur);


--
-- TOC entry 5193 (class 2606 OID 16988)
-- Name: attendance_devices attendance_devices_cloud_id_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices
    ADD CONSTRAINT attendance_devices_cloud_id_unique UNIQUE (cloud_id);


--
-- TOC entry 5195 (class 2606 OID 16990)
-- Name: attendance_devices attendance_devices_device_sn_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices
    ADD CONSTRAINT attendance_devices_device_sn_unique UNIQUE (device_sn);


--
-- TOC entry 5197 (class 2606 OID 16992)
-- Name: attendance_devices attendance_devices_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices
    ADD CONSTRAINT attendance_devices_pkey PRIMARY KEY (id_device);


--
-- TOC entry 5199 (class 2606 OID 16994)
-- Name: attendance_gps attendance_gps_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_gps
    ADD CONSTRAINT attendance_gps_pkey PRIMARY KEY (id_att_gps);


--
-- TOC entry 5201 (class 2606 OID 16996)
-- Name: attendance_karyawan_grup attendance_karyawan_grup_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_karyawan_grup
    ADD CONSTRAINT attendance_karyawan_grup_pkey PRIMARY KEY (id);


--
-- TOC entry 5203 (class 2606 OID 16998)
-- Name: attendance_scanlogs attendance_scanlogs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_scanlogs
    ADD CONSTRAINT attendance_scanlogs_pkey PRIMARY KEY (id_scanlog);


--
-- TOC entry 5205 (class 2606 OID 17000)
-- Name: attendance_summaries attendance_summaries_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_summaries
    ADD CONSTRAINT attendance_summaries_pkey PRIMARY KEY (id_att_summary);


--
-- TOC entry 5209 (class 2606 OID 17002)
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- TOC entry 5207 (class 2606 OID 17004)
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- TOC entry 5211 (class 2606 OID 17006)
-- Name: cleareance_details cleareance_details_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_details
    ADD CONSTRAINT cleareance_details_pkey PRIMARY KEY (id_cleareance_detail);


--
-- TOC entry 5213 (class 2606 OID 17008)
-- Name: cleareance_settings cleareance_settings_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_settings
    ADD CONSTRAINT cleareance_settings_pkey PRIMARY KEY (id_cleareance_setting);


--
-- TOC entry 5215 (class 2606 OID 17010)
-- Name: cleareances cleareances_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareances
    ADD CONSTRAINT cleareances_pkey PRIMARY KEY (id_cleareance);


--
-- TOC entry 5217 (class 2606 OID 17012)
-- Name: cutis cutis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cutis
    ADD CONSTRAINT cutis_pkey PRIMARY KEY (id_cuti);


--
-- TOC entry 5219 (class 2606 OID 17014)
-- Name: departemens departemens_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.departemens
    ADD CONSTRAINT departemens_pkey PRIMARY KEY (id_departemen);


--
-- TOC entry 5221 (class 2606 OID 17016)
-- Name: detail_lemburs detail_lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs
    ADD CONSTRAINT detail_lemburs_pkey PRIMARY KEY (id_detail_lembur);


--
-- TOC entry 5223 (class 2606 OID 17018)
-- Name: detail_millages detail_millages_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_millages
    ADD CONSTRAINT detail_millages_pkey PRIMARY KEY (id_detail_millage);


--
-- TOC entry 5225 (class 2606 OID 17020)
-- Name: detail_tugasluars detail_tugasluars_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars
    ADD CONSTRAINT detail_tugasluars_pkey PRIMARY KEY (id_detail_tugasluar);


--
-- TOC entry 5227 (class 2606 OID 17022)
-- Name: divisis divisis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.divisis
    ADD CONSTRAINT divisis_pkey PRIMARY KEY (id_divisi);


--
-- TOC entry 5229 (class 2606 OID 17024)
-- Name: events events_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.events
    ADD CONSTRAINT events_pkey PRIMARY KEY (id_event);


--
-- TOC entry 5231 (class 2606 OID 17026)
-- Name: export_slip_lemburs export_slip_lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.export_slip_lemburs
    ADD CONSTRAINT export_slip_lemburs_pkey PRIMARY KEY (id_export_slip_lembur);


--
-- TOC entry 5233 (class 2606 OID 17028)
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5235 (class 2606 OID 17030)
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- TOC entry 5237 (class 2606 OID 17032)
-- Name: gaji_departemens gaji_departemens_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.gaji_departemens
    ADD CONSTRAINT gaji_departemens_pkey PRIMARY KEY (id_gaji_departemen);


--
-- TOC entry 5239 (class 2606 OID 17034)
-- Name: grup_patterns grup_patterns_nama_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grup_patterns
    ADD CONSTRAINT grup_patterns_nama_unique UNIQUE (nama);


--
-- TOC entry 5241 (class 2606 OID 17036)
-- Name: grup_patterns grup_patterns_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grup_patterns
    ADD CONSTRAINT grup_patterns_pkey PRIMARY KEY (id_grup_pattern);


--
-- TOC entry 5243 (class 2606 OID 17038)
-- Name: grups grups_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grups
    ADD CONSTRAINT grups_pkey PRIMARY KEY (id_grup);


--
-- TOC entry 5245 (class 2606 OID 17040)
-- Name: izins izins_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.izins
    ADD CONSTRAINT izins_pkey PRIMARY KEY (id_izin);


--
-- TOC entry 5247 (class 2606 OID 17042)
-- Name: jabatans jabatans_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jabatans
    ADD CONSTRAINT jabatans_pkey PRIMARY KEY (id_jabatan);


--
-- TOC entry 5249 (class 2606 OID 17044)
-- Name: jenis_cutis jenis_cutis_jenis_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jenis_cutis
    ADD CONSTRAINT jenis_cutis_jenis_unique UNIQUE (jenis);


--
-- TOC entry 5251 (class 2606 OID 17046)
-- Name: jenis_cutis jenis_cutis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jenis_cutis
    ADD CONSTRAINT jenis_cutis_pkey PRIMARY KEY (id_jenis_cuti);


--
-- TOC entry 5253 (class 2606 OID 17048)
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- TOC entry 5255 (class 2606 OID 17050)
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- TOC entry 5258 (class 2606 OID 17052)
-- Name: karyawan_posisi karyawan_posisi_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawan_posisi
    ADD CONSTRAINT karyawan_posisi_pkey PRIMARY KEY (id);


--
-- TOC entry 5260 (class 2606 OID 17054)
-- Name: karyawans karyawans_email_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawans
    ADD CONSTRAINT karyawans_email_unique UNIQUE (email);


--
-- TOC entry 5262 (class 2606 OID 17056)
-- Name: karyawans karyawans_ni_karyawan_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawans
    ADD CONSTRAINT karyawans_ni_karyawan_unique UNIQUE (ni_karyawan);


--
-- TOC entry 5264 (class 2606 OID 17058)
-- Name: karyawans karyawans_no_telp_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawans
    ADD CONSTRAINT karyawans_no_telp_unique UNIQUE (no_telp);


--
-- TOC entry 5266 (class 2606 OID 17060)
-- Name: karyawans karyawans_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawans
    ADD CONSTRAINT karyawans_pkey PRIMARY KEY (id_karyawan);


--
-- TOC entry 5268 (class 2606 OID 17062)
-- Name: kontraks kontraks_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.kontraks
    ADD CONSTRAINT kontraks_pkey PRIMARY KEY (id_kontrak);


--
-- TOC entry 5272 (class 2606 OID 17064)
-- Name: ksk_change_histories ksk_change_histories_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_change_histories
    ADD CONSTRAINT ksk_change_histories_pkey PRIMARY KEY (id_ksk_change_history);


--
-- TOC entry 5274 (class 2606 OID 17066)
-- Name: ksk_details ksk_details_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details
    ADD CONSTRAINT ksk_details_pkey PRIMARY KEY (id_ksk_detail);


--
-- TOC entry 5270 (class 2606 OID 17068)
-- Name: ksk ksk_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk
    ADD CONSTRAINT ksk_pkey PRIMARY KEY (id_ksk);


--
-- TOC entry 5276 (class 2606 OID 17070)
-- Name: lembur_harians lembur_harians_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lembur_harians
    ADD CONSTRAINT lembur_harians_pkey PRIMARY KEY (id_lembur_harian);


--
-- TOC entry 5278 (class 2606 OID 17072)
-- Name: lemburs lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lemburs
    ADD CONSTRAINT lemburs_pkey PRIMARY KEY (id_lembur);


--
-- TOC entry 5280 (class 2606 OID 17074)
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- TOC entry 5282 (class 2606 OID 17076)
-- Name: millages millages_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.millages
    ADD CONSTRAINT millages_pkey PRIMARY KEY (id_millage);


--
-- TOC entry 5285 (class 2606 OID 17078)
-- Name: model_has_permissions model_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_pkey PRIMARY KEY (permission_id, model_id, model_type);


--
-- TOC entry 5288 (class 2606 OID 17080)
-- Name: model_has_roles model_has_roles_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_pkey PRIMARY KEY (role_id, model_id, model_type);


--
-- TOC entry 5290 (class 2606 OID 17082)
-- Name: organisasis organisasis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.organisasis
    ADD CONSTRAINT organisasis_pkey PRIMARY KEY (id_organisasi);


--
-- TOC entry 5292 (class 2606 OID 17084)
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- TOC entry 5294 (class 2606 OID 17086)
-- Name: permissions permissions_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_name_guard_name_unique UNIQUE (name, guard_name);


--
-- TOC entry 5296 (class 2606 OID 17088)
-- Name: permissions permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.permissions
    ADD CONSTRAINT permissions_pkey PRIMARY KEY (id);


--
-- TOC entry 5298 (class 2606 OID 17090)
-- Name: personal_access_tokens personal_access_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_pkey PRIMARY KEY (id);


--
-- TOC entry 5300 (class 2606 OID 17092)
-- Name: personal_access_tokens personal_access_tokens_token_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.personal_access_tokens
    ADD CONSTRAINT personal_access_tokens_token_unique UNIQUE (token);


--
-- TOC entry 5303 (class 2606 OID 17094)
-- Name: pikets pikets_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.pikets
    ADD CONSTRAINT pikets_pkey PRIMARY KEY (id_piket);


--
-- TOC entry 5305 (class 2606 OID 17096)
-- Name: posisis posisis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.posisis
    ADD CONSTRAINT posisis_pkey PRIMARY KEY (id_posisi);


--
-- TOC entry 5307 (class 2606 OID 17098)
-- Name: rekap_lembur rekap_lembur_karyawan_id_periode_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur
    ADD CONSTRAINT rekap_lembur_karyawan_id_periode_unique UNIQUE (karyawan_id, periode);


--
-- TOC entry 5309 (class 2606 OID 17100)
-- Name: rekap_lembur rekap_lembur_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur
    ADD CONSTRAINT rekap_lembur_pkey PRIMARY KEY (id);


--
-- TOC entry 5311 (class 2606 OID 17102)
-- Name: rekap_lembur_summary rekap_lembur_summary_departemen_periode_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur_summary
    ADD CONSTRAINT rekap_lembur_summary_departemen_periode_unique UNIQUE (departemen, periode);


--
-- TOC entry 5313 (class 2606 OID 17104)
-- Name: rekap_lembur_summary rekap_lembur_summary_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.rekap_lembur_summary
    ADD CONSTRAINT rekap_lembur_summary_pkey PRIMARY KEY (id);


--
-- TOC entry 5315 (class 2606 OID 17106)
-- Name: reset_cutis reset_cutis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.reset_cutis
    ADD CONSTRAINT reset_cutis_pkey PRIMARY KEY (id_reset_cuti);


--
-- TOC entry 5317 (class 2606 OID 17108)
-- Name: role_has_permissions role_has_permissions_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_pkey PRIMARY KEY (permission_id, role_id);


--
-- TOC entry 5319 (class 2606 OID 17110)
-- Name: roles roles_name_guard_name_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_name_guard_name_unique UNIQUE (name, guard_name);


--
-- TOC entry 5321 (class 2606 OID 17112)
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- TOC entry 5323 (class 2606 OID 17114)
-- Name: sakits sakits_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sakits
    ADD CONSTRAINT sakits_pkey PRIMARY KEY (id_sakit);


--
-- TOC entry 5325 (class 2606 OID 17116)
-- Name: seksis seksis_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.seksis
    ADD CONSTRAINT seksis_pkey PRIMARY KEY (id_seksi);


--
-- TOC entry 5328 (class 2606 OID 17118)
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- TOC entry 5331 (class 2606 OID 17120)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_karyawan_id_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_karyawan_id_unique UNIQUE (karyawan_id);


--
-- TOC entry 5333 (class 2606 OID 17122)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_pkey PRIMARY KEY (id_setting_lembur_karyawan);


--
-- TOC entry 5335 (class 2606 OID 17124)
-- Name: setting_lemburs setting_lemburs_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lemburs
    ADD CONSTRAINT setting_lemburs_pkey PRIMARY KEY (id_setting_lembur);


--
-- TOC entry 5337 (class 2606 OID 17126)
-- Name: setting_tugasluars setting_tugasluars_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_tugasluars
    ADD CONSTRAINT setting_tugasluars_pkey PRIMARY KEY (id_setting_tugasluar);


--
-- TOC entry 5339 (class 2606 OID 17128)
-- Name: slip_lembur_karyawans slip_lembur_karyawans_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.slip_lembur_karyawans
    ADD CONSTRAINT slip_lembur_karyawans_pkey PRIMARY KEY (id);


--
-- TOC entry 5343 (class 2606 OID 17130)
-- Name: sto_headers sto_headers_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_headers
    ADD CONSTRAINT sto_headers_pkey PRIMARY KEY (id_sto_header);


--
-- TOC entry 5345 (class 2606 OID 17132)
-- Name: sto_lines sto_lines_no_label_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_lines
    ADD CONSTRAINT sto_lines_no_label_unique UNIQUE (no_label);


--
-- TOC entry 5347 (class 2606 OID 17134)
-- Name: sto_lines sto_lines_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_lines
    ADD CONSTRAINT sto_lines_pkey PRIMARY KEY (id_sto_line);


--
-- TOC entry 5349 (class 2606 OID 17136)
-- Name: sto_upload sto_upload_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_upload
    ADD CONSTRAINT sto_upload_pkey PRIMARY KEY (id_sto_upload);


--
-- TOC entry 5351 (class 2606 OID 17138)
-- Name: templates templates_nama_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.templates
    ADD CONSTRAINT templates_nama_unique UNIQUE (nama);


--
-- TOC entry 5353 (class 2606 OID 17140)
-- Name: templates templates_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.templates
    ADD CONSTRAINT templates_pkey PRIMARY KEY (id_template);


--
-- TOC entry 5355 (class 2606 OID 17142)
-- Name: tugasluars tugasluars_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.tugasluars
    ADD CONSTRAINT tugasluars_pkey PRIMARY KEY (id_tugasluar);


--
-- TOC entry 5357 (class 2606 OID 17144)
-- Name: turnovers turnovers_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.turnovers
    ADD CONSTRAINT turnovers_pkey PRIMARY KEY (id_turnover);


--
-- TOC entry 5341 (class 2606 OID 17146)
-- Name: slip_lembur_karyawans unique_slip_lembur; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.slip_lembur_karyawans
    ADD CONSTRAINT unique_slip_lembur UNIQUE (karyawan_id, periode, organisasi_id);


--
-- TOC entry 5359 (class 2606 OID 17148)
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- TOC entry 5361 (class 2606 OID 17150)
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- TOC entry 5179 (class 1259 OID 17151)
-- Name: activity_log_log_name_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX activity_log_log_name_index ON public.activity_log USING btree (log_name);


--
-- TOC entry 5182 (class 1259 OID 17152)
-- Name: causer; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX causer ON public.activity_log USING btree (causer_type, causer_id);


--
-- TOC entry 5256 (class 1259 OID 17153)
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- TOC entry 5283 (class 1259 OID 17154)
-- Name: model_has_permissions_model_id_model_type_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX model_has_permissions_model_id_model_type_index ON public.model_has_permissions USING btree (model_id, model_type);


--
-- TOC entry 5286 (class 1259 OID 17155)
-- Name: model_has_roles_model_id_model_type_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX model_has_roles_model_id_model_type_index ON public.model_has_roles USING btree (model_id, model_type);


--
-- TOC entry 5301 (class 1259 OID 17156)
-- Name: personal_access_tokens_tokenable_type_tokenable_id_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX personal_access_tokens_tokenable_type_tokenable_id_index ON public.personal_access_tokens USING btree (tokenable_type, tokenable_id);


--
-- TOC entry 5326 (class 1259 OID 17157)
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- TOC entry 5329 (class 1259 OID 17158)
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- TOC entry 5183 (class 1259 OID 17159)
-- Name: subject; Type: INDEX; Schema: public; Owner: ict
--

CREATE INDEX subject ON public.activity_log USING btree (subject_type, subject_id);


--
-- TOC entry 5362 (class 2606 OID 17160)
-- Name: approval_cutis approval_cutis_cuti_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.approval_cutis
    ADD CONSTRAINT approval_cutis_cuti_id_foreign FOREIGN KEY (cuti_id) REFERENCES public.cutis(id_cuti) ON DELETE CASCADE;


--
-- TOC entry 5363 (class 2606 OID 17165)
-- Name: attachment_ksk_details attachment_ksk_details_ksk_detail_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_ksk_details
    ADD CONSTRAINT attachment_ksk_details_ksk_detail_id_foreign FOREIGN KEY (ksk_detail_id) REFERENCES public.ksk_details(id_ksk_detail) ON DELETE CASCADE;


--
-- TOC entry 5364 (class 2606 OID 17170)
-- Name: attachment_lemburs attachment_lemburs_lembur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attachment_lemburs
    ADD CONSTRAINT attachment_lemburs_lembur_id_foreign FOREIGN KEY (lembur_id) REFERENCES public.lemburs(id_lembur) ON DELETE CASCADE;


--
-- TOC entry 5365 (class 2606 OID 17175)
-- Name: attendance_devices attendance_devices_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_devices
    ADD CONSTRAINT attendance_devices_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5366 (class 2606 OID 17180)
-- Name: attendance_gps attendance_gps_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_gps
    ADD CONSTRAINT attendance_gps_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5367 (class 2606 OID 17185)
-- Name: attendance_gps attendance_gps_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_gps
    ADD CONSTRAINT attendance_gps_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5368 (class 2606 OID 17190)
-- Name: attendance_karyawan_grup attendance_karyawan_grup_grup_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_karyawan_grup
    ADD CONSTRAINT attendance_karyawan_grup_grup_id_foreign FOREIGN KEY (grup_id) REFERENCES public.grups(id_grup) ON DELETE RESTRICT;


--
-- TOC entry 5369 (class 2606 OID 17195)
-- Name: attendance_karyawan_grup attendance_karyawan_grup_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_karyawan_grup
    ADD CONSTRAINT attendance_karyawan_grup_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5370 (class 2606 OID 17200)
-- Name: attendance_scanlogs attendance_scanlogs_device_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_scanlogs
    ADD CONSTRAINT attendance_scanlogs_device_id_foreign FOREIGN KEY (device_id) REFERENCES public.attendance_devices(id_device) ON DELETE RESTRICT;


--
-- TOC entry 5371 (class 2606 OID 17205)
-- Name: attendance_scanlogs attendance_scanlogs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_scanlogs
    ADD CONSTRAINT attendance_scanlogs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5372 (class 2606 OID 17210)
-- Name: attendance_summaries attendance_summaries_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_summaries
    ADD CONSTRAINT attendance_summaries_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5373 (class 2606 OID 17215)
-- Name: attendance_summaries attendance_summaries_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.attendance_summaries
    ADD CONSTRAINT attendance_summaries_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5374 (class 2606 OID 17220)
-- Name: cleareance_details cleareance_details_cleareance_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_details
    ADD CONSTRAINT cleareance_details_cleareance_id_foreign FOREIGN KEY (cleareance_id) REFERENCES public.cleareances(id_cleareance) ON DELETE RESTRICT;


--
-- TOC entry 5375 (class 2606 OID 17225)
-- Name: cleareance_details cleareance_details_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_details
    ADD CONSTRAINT cleareance_details_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5376 (class 2606 OID 17230)
-- Name: cleareance_settings cleareance_settings_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareance_settings
    ADD CONSTRAINT cleareance_settings_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5377 (class 2606 OID 17235)
-- Name: cleareances cleareances_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareances
    ADD CONSTRAINT cleareances_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE CASCADE;


--
-- TOC entry 5378 (class 2606 OID 17240)
-- Name: cleareances cleareances_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cleareances
    ADD CONSTRAINT cleareances_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE CASCADE;


--
-- TOC entry 5379 (class 2606 OID 17245)
-- Name: cutis cutis_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.cutis
    ADD CONSTRAINT cutis_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5380 (class 2606 OID 17250)
-- Name: departemens departemens_divisi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.departemens
    ADD CONSTRAINT departemens_divisi_id_foreign FOREIGN KEY (divisi_id) REFERENCES public.divisis(id_divisi) ON DELETE RESTRICT;


--
-- TOC entry 5381 (class 2606 OID 17255)
-- Name: detail_lemburs detail_lemburs_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs
    ADD CONSTRAINT detail_lemburs_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5382 (class 2606 OID 17260)
-- Name: detail_lemburs detail_lemburs_lembur_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs
    ADD CONSTRAINT detail_lemburs_lembur_id_foreign FOREIGN KEY (lembur_id) REFERENCES public.lemburs(id_lembur) ON DELETE RESTRICT;


--
-- TOC entry 5383 (class 2606 OID 17265)
-- Name: detail_lemburs detail_lemburs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_lemburs
    ADD CONSTRAINT detail_lemburs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5384 (class 2606 OID 17270)
-- Name: detail_millages detail_millages_millage_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_millages
    ADD CONSTRAINT detail_millages_millage_id_foreign FOREIGN KEY (millage_id) REFERENCES public.millages(id_millage) ON DELETE RESTRICT;


--
-- TOC entry 5385 (class 2606 OID 17275)
-- Name: detail_millages detail_millages_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_millages
    ADD CONSTRAINT detail_millages_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5386 (class 2606 OID 17280)
-- Name: detail_tugasluars detail_tugasluars_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars
    ADD CONSTRAINT detail_tugasluars_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5387 (class 2606 OID 17285)
-- Name: detail_tugasluars detail_tugasluars_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars
    ADD CONSTRAINT detail_tugasluars_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5388 (class 2606 OID 17290)
-- Name: detail_tugasluars detail_tugasluars_tugasluar_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.detail_tugasluars
    ADD CONSTRAINT detail_tugasluars_tugasluar_id_foreign FOREIGN KEY (tugasluar_id) REFERENCES public.tugasluars(id_tugasluar) ON DELETE RESTRICT;


--
-- TOC entry 5389 (class 2606 OID 17295)
-- Name: export_slip_lemburs export_slip_lemburs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.export_slip_lemburs
    ADD CONSTRAINT export_slip_lemburs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5390 (class 2606 OID 17300)
-- Name: gaji_departemens gaji_departemens_departemen_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.gaji_departemens
    ADD CONSTRAINT gaji_departemens_departemen_id_foreign FOREIGN KEY (departemen_id) REFERENCES public.departemens(id_departemen) ON DELETE RESTRICT;


--
-- TOC entry 5391 (class 2606 OID 17305)
-- Name: gaji_departemens gaji_departemens_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.gaji_departemens
    ADD CONSTRAINT gaji_departemens_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE CASCADE;


--
-- TOC entry 5392 (class 2606 OID 17310)
-- Name: grup_patterns grup_patterns_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.grup_patterns
    ADD CONSTRAINT grup_patterns_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5393 (class 2606 OID 17315)
-- Name: izins izins_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.izins
    ADD CONSTRAINT izins_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5394 (class 2606 OID 17320)
-- Name: izins izins_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.izins
    ADD CONSTRAINT izins_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5395 (class 2606 OID 17325)
-- Name: karyawan_posisi karyawan_posisi_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawan_posisi
    ADD CONSTRAINT karyawan_posisi_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE CASCADE;


--
-- TOC entry 5396 (class 2606 OID 17330)
-- Name: karyawan_posisi karyawan_posisi_posisi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.karyawan_posisi
    ADD CONSTRAINT karyawan_posisi_posisi_id_foreign FOREIGN KEY (posisi_id) REFERENCES public.posisis(id_posisi) ON DELETE CASCADE;


--
-- TOC entry 5397 (class 2606 OID 17335)
-- Name: kontraks kontraks_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.kontraks
    ADD CONSTRAINT kontraks_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5399 (class 2606 OID 17340)
-- Name: ksk_change_histories ksk_change_histories_changed_by_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_change_histories
    ADD CONSTRAINT ksk_change_histories_changed_by_id_foreign FOREIGN KEY (changed_by_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5400 (class 2606 OID 17345)
-- Name: ksk_change_histories ksk_change_histories_ksk_detail_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_change_histories
    ADD CONSTRAINT ksk_change_histories_ksk_detail_id_foreign FOREIGN KEY (ksk_detail_id) REFERENCES public.ksk_details(id_ksk_detail) ON DELETE RESTRICT;


--
-- TOC entry 5401 (class 2606 OID 17350)
-- Name: ksk_details ksk_details_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details
    ADD CONSTRAINT ksk_details_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5402 (class 2606 OID 17355)
-- Name: ksk_details ksk_details_ksk_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details
    ADD CONSTRAINT ksk_details_ksk_id_foreign FOREIGN KEY (ksk_id) REFERENCES public.ksk(id_ksk) ON DELETE RESTRICT;


--
-- TOC entry 5403 (class 2606 OID 17360)
-- Name: ksk_details ksk_details_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk_details
    ADD CONSTRAINT ksk_details_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5398 (class 2606 OID 17365)
-- Name: ksk ksk_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.ksk
    ADD CONSTRAINT ksk_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5404 (class 2606 OID 17370)
-- Name: lembur_harians lembur_harians_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lembur_harians
    ADD CONSTRAINT lembur_harians_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5405 (class 2606 OID 17375)
-- Name: lemburs lemburs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.lemburs
    ADD CONSTRAINT lemburs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5406 (class 2606 OID 17380)
-- Name: millages millages_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.millages
    ADD CONSTRAINT millages_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5407 (class 2606 OID 17385)
-- Name: millages millages_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.millages
    ADD CONSTRAINT millages_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5408 (class 2606 OID 17390)
-- Name: model_has_permissions model_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.model_has_permissions
    ADD CONSTRAINT model_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- TOC entry 5409 (class 2606 OID 17395)
-- Name: model_has_roles model_has_roles_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.model_has_roles
    ADD CONSTRAINT model_has_roles_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- TOC entry 5410 (class 2606 OID 17400)
-- Name: pikets pikets_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.pikets
    ADD CONSTRAINT pikets_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5411 (class 2606 OID 17405)
-- Name: pikets pikets_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.pikets
    ADD CONSTRAINT pikets_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5412 (class 2606 OID 17410)
-- Name: posisis posisis_jabatan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.posisis
    ADD CONSTRAINT posisis_jabatan_id_foreign FOREIGN KEY (jabatan_id) REFERENCES public.jabatans(id_jabatan) ON DELETE RESTRICT;


--
-- TOC entry 5413 (class 2606 OID 17415)
-- Name: role_has_permissions role_has_permissions_permission_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_permission_id_foreign FOREIGN KEY (permission_id) REFERENCES public.permissions(id) ON DELETE CASCADE;


--
-- TOC entry 5414 (class 2606 OID 17420)
-- Name: role_has_permissions role_has_permissions_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.role_has_permissions
    ADD CONSTRAINT role_has_permissions_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE CASCADE;


--
-- TOC entry 5415 (class 2606 OID 17425)
-- Name: sakits sakits_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sakits
    ADD CONSTRAINT sakits_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5416 (class 2606 OID 17430)
-- Name: sakits sakits_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sakits
    ADD CONSTRAINT sakits_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5417 (class 2606 OID 17435)
-- Name: seksis seksis_departemen_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.seksis
    ADD CONSTRAINT seksis_departemen_id_foreign FOREIGN KEY (departemen_id) REFERENCES public.departemens(id_departemen) ON DELETE RESTRICT;


--
-- TOC entry 5418 (class 2606 OID 17440)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_jabatan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_jabatan_id_foreign FOREIGN KEY (jabatan_id) REFERENCES public.jabatans(id_jabatan) ON DELETE RESTRICT;


--
-- TOC entry 5419 (class 2606 OID 17445)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5420 (class 2606 OID 17450)
-- Name: setting_lembur_karyawans setting_lembur_karyawans_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lembur_karyawans
    ADD CONSTRAINT setting_lembur_karyawans_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5421 (class 2606 OID 17455)
-- Name: setting_lemburs setting_lemburs_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_lemburs
    ADD CONSTRAINT setting_lemburs_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5422 (class 2606 OID 17460)
-- Name: setting_tugasluars setting_tugasluars_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.setting_tugasluars
    ADD CONSTRAINT setting_tugasluars_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5423 (class 2606 OID 17465)
-- Name: sto_lines sto_lines_sto_header_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.sto_lines
    ADD CONSTRAINT sto_lines_sto_header_id_foreign FOREIGN KEY (sto_header_id) REFERENCES public.sto_headers(id_sto_header) ON DELETE CASCADE;


--
-- TOC entry 5424 (class 2606 OID 17470)
-- Name: tugasluars tugasluars_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.tugasluars
    ADD CONSTRAINT tugasluars_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


--
-- TOC entry 5425 (class 2606 OID 17475)
-- Name: tugasluars tugasluars_organisasi_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.tugasluars
    ADD CONSTRAINT tugasluars_organisasi_id_foreign FOREIGN KEY (organisasi_id) REFERENCES public.organisasis(id_organisasi) ON DELETE RESTRICT;


--
-- TOC entry 5426 (class 2606 OID 17480)
-- Name: turnovers turnovers_karyawan_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: ict
--

ALTER TABLE ONLY public.turnovers
    ADD CONSTRAINT turnovers_karyawan_id_foreign FOREIGN KEY (karyawan_id) REFERENCES public.karyawans(id_karyawan) ON DELETE RESTRICT;


-- Completed on 2025-09-20 17:28:24

--
-- PostgreSQL database dump complete
--

