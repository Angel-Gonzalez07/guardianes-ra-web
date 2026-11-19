--
-- PostgreSQL database dump
--

\restrict PPTfK3M9fveglEr257YG0scyfc1lV8dqRqHfgU7bcXpeCUfqdlAlVF7fXug85N5

-- Dumped from database version 18.6
-- Dumped by pg_dump version 18.6

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
-- Name: cache; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache (
    key character varying(255) NOT NULL,
    value text NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: cache_locks; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.cache_locks (
    key character varying(255) NOT NULL,
    owner character varying(255) NOT NULL,
    expiration bigint NOT NULL
);


--
-- Name: categorias; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.categorias (
    id bigint NOT NULL,
    nombre character varying(80) NOT NULL,
    descripcion character varying(255),
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: categorias_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.categorias_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: categorias_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.categorias_id_seq OWNED BY public.categorias.id;


--
-- Name: failed_jobs; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.failed_jobs (
    id bigint NOT NULL,
    uuid character varying(255) NOT NULL,
    connection character varying(255) NOT NULL,
    queue character varying(255) NOT NULL,
    payload text NOT NULL,
    exception text NOT NULL,
    failed_at timestamp(0) without time zone DEFAULT CURRENT_TIMESTAMP NOT NULL
);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.failed_jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.failed_jobs_id_seq OWNED BY public.failed_jobs.id;


--
-- Name: job_batches; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: jobs; Type: TABLE; Schema: public; Owner: -
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


--
-- Name: jobs_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.jobs_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: jobs_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.jobs_id_seq OWNED BY public.jobs.id;


--
-- Name: migrations; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.migrations (
    id integer NOT NULL,
    migration character varying(255) NOT NULL,
    batch integer NOT NULL
);


--
-- Name: migrations_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.migrations_id_seq
    AS integer
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: migrations_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.migrations_id_seq OWNED BY public.migrations.id;


--
-- Name: movimientos_inventario; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.movimientos_inventario (
    id bigint NOT NULL,
    producto_id bigint NOT NULL,
    tipo character varying(10) NOT NULL,
    cantidad integer NOT NULL,
    stock_resultante integer NOT NULL,
    motivo character varying(30) NOT NULL,
    referencia character varying(30),
    user_id bigint,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: movimientos_inventario_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.movimientos_inventario_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: movimientos_inventario_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.movimientos_inventario_id_seq OWNED BY public.movimientos_inventario.id;


--
-- Name: orden_compra_detalles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.orden_compra_detalles (
    id bigint NOT NULL,
    orden_compra_id bigint NOT NULL,
    producto_id bigint NOT NULL,
    nombre character varying(120) NOT NULL,
    cantidad integer NOT NULL,
    costo_unitario numeric(10,2) NOT NULL,
    subtotal numeric(10,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: orden_compra_detalles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.orden_compra_detalles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: orden_compra_detalles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.orden_compra_detalles_id_seq OWNED BY public.orden_compra_detalles.id;


--
-- Name: ordenes_compra; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ordenes_compra (
    id bigint NOT NULL,
    folio character varying(20) NOT NULL,
    proveedor_id bigint NOT NULL,
    user_id bigint NOT NULL,
    estado character varying(20) DEFAULT 'borrador'::character varying NOT NULL,
    total numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    notas character varying(200),
    enviada_at timestamp(0) without time zone,
    recibida_at timestamp(0) without time zone,
    cancelada_at timestamp(0) without time zone,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: ordenes_compra_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ordenes_compra_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ordenes_compra_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ordenes_compra_id_seq OWNED BY public.ordenes_compra.id;


--
-- Name: password_reset_tokens; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.password_reset_tokens (
    email character varying(255) NOT NULL,
    token character varying(255) NOT NULL,
    created_at timestamp(0) without time zone
);


--
-- Name: productos; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.productos (
    id bigint NOT NULL,
    categoria_id bigint NOT NULL,
    proveedor_id bigint NOT NULL,
    nombre character varying(120) NOT NULL,
    sku character varying(30) NOT NULL,
    descripcion text,
    guardian character varying(30),
    precio numeric(10,2) NOT NULL,
    stock integer DEFAULT 0 NOT NULL,
    imagen character varying(255),
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: productos_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.productos_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: productos_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.productos_id_seq OWNED BY public.productos.id;


--
-- Name: proveedores; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.proveedores (
    id bigint NOT NULL,
    nombre character varying(120) NOT NULL,
    contacto character varying(100) NOT NULL,
    email character varying(150) NOT NULL,
    telefono character varying(15) NOT NULL,
    direccion character varying(200),
    activo boolean DEFAULT true NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    rfc character varying(13),
    ciudad character varying(80),
    estado_republica character varying(40),
    dias_entrega smallint,
    condiciones_pago character varying(40),
    notas text
);


--
-- Name: proveedores_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.proveedores_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: proveedores_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.proveedores_id_seq OWNED BY public.proveedores.id;


--
-- Name: roles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.roles (
    id bigint NOT NULL,
    nombre character varying(30) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: roles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.roles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: roles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.roles_id_seq OWNED BY public.roles.id;


--
-- Name: sessions; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.sessions (
    id character varying(255) NOT NULL,
    user_id bigint,
    ip_address character varying(45),
    user_agent text,
    payload text NOT NULL,
    last_activity integer NOT NULL
);


--
-- Name: users; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.users (
    id bigint NOT NULL,
    name character varying(255) NOT NULL,
    email character varying(255) NOT NULL,
    email_verified_at timestamp(0) without time zone,
    password character varying(255) NOT NULL,
    remember_token character varying(100),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    role_id bigint NOT NULL,
    activo boolean DEFAULT true NOT NULL,
    nombres character varying(60),
    apellidos character varying(60)
);


--
-- Name: users_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.users_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: users_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.users_id_seq OWNED BY public.users.id;


--
-- Name: venta_detalles; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.venta_detalles (
    id bigint NOT NULL,
    venta_id bigint NOT NULL,
    producto_id bigint NOT NULL,
    nombre character varying(120) NOT NULL,
    precio_unitario numeric(10,2) NOT NULL,
    cantidad integer NOT NULL,
    subtotal numeric(10,2) NOT NULL,
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone
);


--
-- Name: venta_detalles_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.venta_detalles_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: venta_detalles_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.venta_detalles_id_seq OWNED BY public.venta_detalles.id;


--
-- Name: ventas; Type: TABLE; Schema: public; Owner: -
--

CREATE TABLE public.ventas (
    id bigint NOT NULL,
    folio character varying(20) NOT NULL,
    user_id bigint NOT NULL,
    total numeric(10,2) NOT NULL,
    estado character varying(20) DEFAULT 'pendiente'::character varying NOT NULL,
    metodo_pago character varying(30) NOT NULL,
    direccion_envio character varying(200),
    created_at timestamp(0) without time zone,
    updated_at timestamp(0) without time zone,
    subtotal numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    costo_envio numeric(10,2) DEFAULT '0'::numeric NOT NULL,
    destinatario character varying(100),
    telefono character varying(10),
    calle_numero character varying(120),
    colonia character varying(80),
    codigo_postal character varying(5),
    ciudad character varying(80),
    estado_republica character varying(40),
    referencias character varying(200),
    paqueteria character varying(40),
    numero_guia character varying(40),
    acepto_terminos_at timestamp(0) without time zone,
    pagada_at timestamp(0) without time zone,
    enviada_at timestamp(0) without time zone,
    entregada_at timestamp(0) without time zone,
    cancelada_at timestamp(0) without time zone
);


--
-- Name: ventas_id_seq; Type: SEQUENCE; Schema: public; Owner: -
--

CREATE SEQUENCE public.ventas_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;


--
-- Name: ventas_id_seq; Type: SEQUENCE OWNED BY; Schema: public; Owner: -
--

ALTER SEQUENCE public.ventas_id_seq OWNED BY public.ventas.id;


--
-- Name: categorias id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categorias ALTER COLUMN id SET DEFAULT nextval('public.categorias_id_seq'::regclass);


--
-- Name: failed_jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs ALTER COLUMN id SET DEFAULT nextval('public.failed_jobs_id_seq'::regclass);


--
-- Name: jobs id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs ALTER COLUMN id SET DEFAULT nextval('public.jobs_id_seq'::regclass);


--
-- Name: migrations id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations ALTER COLUMN id SET DEFAULT nextval('public.migrations_id_seq'::regclass);


--
-- Name: movimientos_inventario id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movimientos_inventario ALTER COLUMN id SET DEFAULT nextval('public.movimientos_inventario_id_seq'::regclass);


--
-- Name: orden_compra_detalles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orden_compra_detalles ALTER COLUMN id SET DEFAULT nextval('public.orden_compra_detalles_id_seq'::regclass);


--
-- Name: ordenes_compra id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ordenes_compra ALTER COLUMN id SET DEFAULT nextval('public.ordenes_compra_id_seq'::regclass);


--
-- Name: productos id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos ALTER COLUMN id SET DEFAULT nextval('public.productos_id_seq'::regclass);


--
-- Name: proveedores id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proveedores ALTER COLUMN id SET DEFAULT nextval('public.proveedores_id_seq'::regclass);


--
-- Name: roles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles ALTER COLUMN id SET DEFAULT nextval('public.roles_id_seq'::regclass);


--
-- Name: users id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users ALTER COLUMN id SET DEFAULT nextval('public.users_id_seq'::regclass);


--
-- Name: venta_detalles id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta_detalles ALTER COLUMN id SET DEFAULT nextval('public.venta_detalles_id_seq'::regclass);


--
-- Name: ventas id; Type: DEFAULT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ventas ALTER COLUMN id SET DEFAULT nextval('public.ventas_id_seq'::regclass);


--
-- Data for Name: categorias; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.categorias (id, nombre, descripcion, activo, created_at, updated_at) FROM stdin;
2	Figuras	Figuras coleccionables de los guardianes	t	2026-11-17 21:29:24	2026-11-17 21:29:24
4	Camisas	Camisas de los guardianes	t	2026-11-17 21:29:24	2026-11-18 15:04:57
3	Peluches	Peluches de los guardianes	t	2026-11-17 21:29:24	2026-11-18 15:05:04
\.


--
-- Data for Name: failed_jobs; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.failed_jobs (id, uuid, connection, queue, payload, exception, failed_at) FROM stdin;
\.


--
-- Data for Name: job_batches; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.job_batches (id, name, total_jobs, pending_jobs, failed_jobs, failed_job_ids, options, cancelled_at, created_at, finished_at) FROM stdin;
\.


--
-- Data for Name: migrations; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.migrations (id, migration, batch) FROM stdin;
1	0001_01_01_000000_create_users_table	1
2	0001_01_01_000001_create_cache_table	1
3	0001_01_01_000002_create_jobs_table	1
4	2026_11_13_012141_create_roles_table	1
5	2026_11_13_012202_add_role_id_and_activo_to_users_table	1
6	2026_11_13_012203_create_categorias_table	1
7	2026_11_13_012204_create_proveedores_table	1
8	2026_11_13_012206_create_clientes_table	1
9	2026_11_13_012253_create_productos_table	1
10	2026_11_14_195810_add_empleado_role	2
11	2026_11_16_234403_create_ventas_table	3
12	2026_11_16_234405_create_venta_detalles_table	3
13	2026_11_18_020403_drop_clientes_table	4
14	2026_11_18_142205_add_nombres_apellidos_to_users_table	4
15	2026_11_18_163808_add_envio_y_seguimiento_to_ventas_table	5
16	2026_11_19_000001_add_datos_comerciales_to_proveedores_table	6
17	2026_11_19_000002_create_ordenes_compra_table	7
18	2026_11_19_000003_create_movimientos_inventario_table	7
\.


--
-- Data for Name: movimientos_inventario; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.movimientos_inventario (id, producto_id, tipo, cantidad, stock_resultante, motivo, referencia, user_id, created_at, updated_at) FROM stdin;
1	6	salida	1	8	venta	GRA-261119-16K9C	5	2026-11-19 16:58:39	2026-11-19 16:58:39
2	7	salida	1	9	venta	GRA-261119-16K9C	5	2026-11-19 16:58:39	2026-11-19 16:58:39
3	3	salida	1	14	venta	GRA-261119-16K9C	5	2026-11-19 16:58:39	2026-11-19 16:58:39
\.


--
-- Data for Name: orden_compra_detalles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.orden_compra_detalles (id, orden_compra_id, producto_id, nombre, cantidad, costo_unitario, subtotal, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: ordenes_compra; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.ordenes_compra (id, folio, proveedor_id, user_id, estado, total, notas, enviada_at, recibida_at, cancelada_at, created_at, updated_at) FROM stdin;
\.


--
-- Data for Name: password_reset_tokens; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.password_reset_tokens (email, token, created_at) FROM stdin;
\.


--
-- Data for Name: productos; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.productos (id, categoria_id, proveedor_id, nombre, sku, descripcion, guardian, precio, stock, imagen, activo, created_at, updated_at) FROM stdin;
5	2	1	Peluche Lumi	PEL-LUM-001	Peluche del Personaje Lumi	Lumi	120.00	10	img/productos/pel-lum-001-1795036087.jpg	t	2026-11-18 15:08:07	2026-11-18 15:08:30
2	3	1	Peluche Goti	PEL-GOT-001	Figura coleccionable de Goti, guardián del ecosistema.	Goti	349.00	13	img/productos/pel-got-001-1795037573.jpg	t	2026-11-17 21:29:24	2026-11-18 15:33:08
4	2	1	Figura Tronqui	FIG-TRO-001	Figura coleccionable de Tronqui, guardián del ecosistema.	Tronqui	399.00	15	img/productos/fig-tro-001-1795038269.jpg	t	2026-11-17 21:29:24	2026-11-18 15:44:29
6	4	1	Camisa Lumi	CAM-LUM-001	Camisa Color Gris Del Personaje LUMI	Lumi	100.00	8	img/productos/cam-lum-001-1795036472.jpg	t	2026-11-18 15:14:32	2026-11-19 16:58:39
7	2	1	Figura Goti	FIG-GOT-001	Figura 3d de Goti	Goti	250.00	9	img/productos/fig-got-001-1795037686.jpg	t	2026-11-18 15:34:46	2026-11-19 16:58:39
3	2	1	Figura Lumi	FIG-LUM-001	Figura coleccionable de Lumi, guardián del ecosistema.	Lumi	349.00	14	img/productos/fig-lum-001-1795036266.jpg	t	2026-11-17 21:29:24	2026-11-19 16:58:39
\.


--
-- Data for Name: proveedores; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.proveedores (id, nombre, contacto, email, telefono, direccion, activo, created_at, updated_at, rfc, ciudad, estado_republica, dias_entrega, condiciones_pago, notas) FROM stdin;
1	Proveedor Demo	Contacto Demo	ventas@proveedordemo.com	2711234567	Córdoba, Veracruz	t	2026-11-17 21:29:24	2026-11-17 21:29:24	\N	\N	\N	\N	\N	\N
\.


--
-- Data for Name: roles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.roles (id, nombre, created_at, updated_at) FROM stdin;
1	admin	2026-11-12 20:41:42	2026-11-12 20:41:42
2	cliente	2026-11-12 20:41:42	2026-11-12 20:41:42
3	empleado	2026-11-14 19:59:10	2026-11-14 19:59:10
\.


--
-- Data for Name: users; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.users (id, name, email, email_verified_at, password, remember_token, created_at, updated_at, role_id, activo, nombres, apellidos) FROM stdin;
5	cliente prueba	cliente@utcv.edu.mx	\N	$2y$12$f3paLS1aGWGaWTbAJPcN8OQ7SSYkxrld4aJoPvPvLgQIMEn8J1nEy	\N	2026-11-17 22:09:13	2026-11-17 22:09:13	2	t	cliente	prueba
6	emilio	20243l691004@utcv.edu.mx	\N	$2y$12$iOEsmPgPyJ9kwYMKnslF6eYFsD2.B0NeM8.F3WG2jJUS3p6Ede0US	\N	2026-11-17 23:36:47	2026-11-17 23:36:47	2	t	emilio	
7	Elio gabriel González romero	eliogabrielgonzalezromero@utcv.edu.mx	\N	$2y$12$A0sWHoCD4uxc94LEschl5uJSPChyEvZ6st9MtbhdbKMc6p.WgQ1Ty	\N	2026-11-17 23:44:38	2026-11-17 23:44:38	2	t	Elio	gabriel González romero
1	Administrador	caejaestudio@utcv.edu.mx	\N	$2y$12$H/0TfAbFhcaDE6L41Z7UsutQT8rh65SC0pjMKG5gMWm0XJ.pIPEE6	\N	2026-11-12 20:41:42	2026-11-18 02:52:28	1	t	Administrador	
11	José	20243l601066@utcv.edu.mx	\N	$2y$12$6nfhs7ZVPXyU0KuitdD81.jQgKn8VNskS374mtXzt3mEsjwcUF91S	\N	2026-11-18 13:35:29	2026-11-18 13:47:52	3	t	José	
9	Angel Gabriel Gonzalez Ubaldo	20243l601029@utcv.edu.mx	\N	$2y$12$MNyo3e0m.TfK2Ou8XMbX2.GJT6mOsk92uN0AzJXMrWgfRfviMa9my	\N	2026-11-18 00:16:12	2026-11-18 14:52:56	1	t	Angel Gabriel	Gonzalez Ubaldo
12	Angel Gabriel Gonzalez Ubaldo	20243l601129@utcv.edu.mx	\N	$2y$12$oZBjEygMazSFX2edawGZ9epdHbQowNJ7fHFZMmzswDNlOuMBwxbXy	\N	2026-11-18 22:12:51	2026-11-18 22:12:51	2	t	Angel Gabriel	Gonzalez Ubaldo
\.


--
-- Data for Name: venta_detalles; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.venta_detalles (id, venta_id, producto_id, nombre, precio_unitario, cantidad, subtotal, created_at, updated_at) FROM stdin;
1	1	2	Figura Goti	349.00	1	349.00	2026-11-17 22:13:06	2026-11-17 22:13:06
2	2	2	Figura Goti	349.00	1	349.00	2026-11-18 13:40:14	2026-11-18 13:40:14
3	3	6	Camisa Lumi	100.00	1	100.00	2026-11-18 22:15:13	2026-11-18 22:15:13
4	4	6	Camisa Lumi	100.00	1	100.00	2026-11-19 16:58:39	2026-11-19 16:58:39
5	4	7	Figura Goti	250.00	1	250.00	2026-11-19 16:58:39	2026-11-19 16:58:39
6	4	3	Figura Lumi	349.00	1	349.00	2026-11-19 16:58:39	2026-11-19 16:58:39
\.


--
-- Data for Name: ventas; Type: TABLE DATA; Schema: public; Owner: -
--

COPY public.ventas (id, folio, user_id, total, estado, metodo_pago, direccion_envio, created_at, updated_at, subtotal, costo_envio, destinatario, telefono, calle_numero, colonia, codigo_postal, ciudad, estado_republica, referencias, paqueteria, numero_guia, acepto_terminos_at, pagada_at, enviada_at, entregada_at, cancelada_at) FROM stdin;
3	GRA-261118-XHDHJ	12	199.00	enviada	Transferencia	Av 8 Calle 27 #2709, Col. Ursulo Galvan, C.P. 94630, Córdoba, Veracruz	2026-11-18 22:15:13	2026-11-18 22:16:58	100.00	99.00	Angel Gabriel Gonzalez Ubaldo	2711781240	Av 8 Calle 27 #2709	Ursulo Galvan	94630	Córdoba	Veracruz	Junto ala talachera de jesus	Correos de México	1234567890	2026-11-18 22:15:13	2026-11-18 22:16:42	2026-11-18 22:16:58	\N	\N
2	GRA-261118-APCOC	11	349.00	enviada	Pago contra entrega	57928626727	2026-11-18 13:40:14	2026-11-18 22:18:37	349.00	0.00	\N	\N	\N	\N	\N	\N	\N	\N	Paquetexpress	32335242424212445	\N	2026-11-18 22:18:24	2026-11-18 22:18:37	\N	\N
4	GRA-261119-16K9C	5	798.00	pendiente	Transferencia	Av 8 Calle 27 #2709, Col. Ursulo Galvan, C.P. 94630, Córdoba, Veracruz	2026-11-19 16:58:39	2026-11-19 16:58:39	699.00	99.00	Angel Gabriel Gonzalez Ubaldo	2711781240	Av 8 Calle 27 #2709	Ursulo Galvan	94630	Córdoba	Veracruz	Junto a la talachera de jesus	\N	\N	2026-11-19 16:58:39	\N	\N	\N	\N
1	GRA-261117-FO5KV	5	349.00	entregada	Tarjeta	av 8 calle 27 numero 1321	2026-11-17 22:13:06	2026-11-19 16:59:50	349.00	0.00	\N	\N	\N	\N	\N	\N	\N	\N	DHL	08976543245678	\N	\N	2026-11-19 16:59:48	2026-11-19 16:59:50	\N
\.


--
-- Name: categorias_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.categorias_id_seq', 4, true);


--
-- Name: failed_jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.failed_jobs_id_seq', 1, false);


--
-- Name: jobs_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.jobs_id_seq', 1, false);


--
-- Name: migrations_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.migrations_id_seq', 18, true);


--
-- Name: movimientos_inventario_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.movimientos_inventario_id_seq', 3, true);


--
-- Name: orden_compra_detalles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.orden_compra_detalles_id_seq', 1, false);


--
-- Name: ordenes_compra_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.ordenes_compra_id_seq', 1, false);


--
-- Name: productos_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.productos_id_seq', 7, true);


--
-- Name: proveedores_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.proveedores_id_seq', 2, true);


--
-- Name: roles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.roles_id_seq', 5, true);


--
-- Name: users_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.users_id_seq', 12, true);


--
-- Name: venta_detalles_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.venta_detalles_id_seq', 6, true);


--
-- Name: ventas_id_seq; Type: SEQUENCE SET; Schema: public; Owner: -
--

SELECT pg_catalog.setval('public.ventas_id_seq', 4, true);


--
-- Name: cache_locks cache_locks_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache_locks
    ADD CONSTRAINT cache_locks_pkey PRIMARY KEY (key);


--
-- Name: cache cache_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.cache
    ADD CONSTRAINT cache_pkey PRIMARY KEY (key);


--
-- Name: categorias categorias_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_nombre_unique UNIQUE (nombre);


--
-- Name: categorias categorias_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.categorias
    ADD CONSTRAINT categorias_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_pkey PRIMARY KEY (id);


--
-- Name: failed_jobs failed_jobs_uuid_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.failed_jobs
    ADD CONSTRAINT failed_jobs_uuid_unique UNIQUE (uuid);


--
-- Name: job_batches job_batches_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.job_batches
    ADD CONSTRAINT job_batches_pkey PRIMARY KEY (id);


--
-- Name: jobs jobs_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.jobs
    ADD CONSTRAINT jobs_pkey PRIMARY KEY (id);


--
-- Name: migrations migrations_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.migrations
    ADD CONSTRAINT migrations_pkey PRIMARY KEY (id);


--
-- Name: movimientos_inventario movimientos_inventario_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movimientos_inventario
    ADD CONSTRAINT movimientos_inventario_pkey PRIMARY KEY (id);


--
-- Name: orden_compra_detalles orden_compra_detalles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orden_compra_detalles
    ADD CONSTRAINT orden_compra_detalles_pkey PRIMARY KEY (id);


--
-- Name: ordenes_compra ordenes_compra_folio_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ordenes_compra
    ADD CONSTRAINT ordenes_compra_folio_unique UNIQUE (folio);


--
-- Name: ordenes_compra ordenes_compra_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ordenes_compra
    ADD CONSTRAINT ordenes_compra_pkey PRIMARY KEY (id);


--
-- Name: password_reset_tokens password_reset_tokens_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.password_reset_tokens
    ADD CONSTRAINT password_reset_tokens_pkey PRIMARY KEY (email);


--
-- Name: productos productos_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_pkey PRIMARY KEY (id);


--
-- Name: productos productos_sku_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_sku_unique UNIQUE (sku);


--
-- Name: proveedores proveedores_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proveedores
    ADD CONSTRAINT proveedores_pkey PRIMARY KEY (id);


--
-- Name: proveedores proveedores_rfc_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.proveedores
    ADD CONSTRAINT proveedores_rfc_unique UNIQUE (rfc);


--
-- Name: roles roles_nombre_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_nombre_unique UNIQUE (nombre);


--
-- Name: roles roles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.roles
    ADD CONSTRAINT roles_pkey PRIMARY KEY (id);


--
-- Name: sessions sessions_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.sessions
    ADD CONSTRAINT sessions_pkey PRIMARY KEY (id);


--
-- Name: users users_email_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_email_unique UNIQUE (email);


--
-- Name: users users_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_pkey PRIMARY KEY (id);


--
-- Name: venta_detalles venta_detalles_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta_detalles
    ADD CONSTRAINT venta_detalles_pkey PRIMARY KEY (id);


--
-- Name: ventas ventas_folio_unique; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ventas
    ADD CONSTRAINT ventas_folio_unique UNIQUE (folio);


--
-- Name: ventas ventas_pkey; Type: CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ventas
    ADD CONSTRAINT ventas_pkey PRIMARY KEY (id);


--
-- Name: cache_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_expiration_index ON public.cache USING btree (expiration);


--
-- Name: cache_locks_expiration_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX cache_locks_expiration_index ON public.cache_locks USING btree (expiration);


--
-- Name: failed_jobs_connection_queue_failed_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX failed_jobs_connection_queue_failed_at_index ON public.failed_jobs USING btree (connection, queue, failed_at);


--
-- Name: jobs_queue_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX jobs_queue_index ON public.jobs USING btree (queue);


--
-- Name: movimientos_inventario_producto_id_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX movimientos_inventario_producto_id_created_at_index ON public.movimientos_inventario USING btree (producto_id, created_at);


--
-- Name: ordenes_compra_estado_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ordenes_compra_estado_created_at_index ON public.ordenes_compra USING btree (estado, created_at);


--
-- Name: sessions_last_activity_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_last_activity_index ON public.sessions USING btree (last_activity);


--
-- Name: sessions_user_id_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX sessions_user_id_index ON public.sessions USING btree (user_id);


--
-- Name: ventas_estado_created_at_index; Type: INDEX; Schema: public; Owner: -
--

CREATE INDEX ventas_estado_created_at_index ON public.ventas USING btree (estado, created_at);


--
-- Name: movimientos_inventario movimientos_inventario_producto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movimientos_inventario
    ADD CONSTRAINT movimientos_inventario_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES public.productos(id) ON DELETE CASCADE;


--
-- Name: movimientos_inventario movimientos_inventario_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.movimientos_inventario
    ADD CONSTRAINT movimientos_inventario_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE SET NULL;


--
-- Name: orden_compra_detalles orden_compra_detalles_orden_compra_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orden_compra_detalles
    ADD CONSTRAINT orden_compra_detalles_orden_compra_id_foreign FOREIGN KEY (orden_compra_id) REFERENCES public.ordenes_compra(id) ON DELETE CASCADE;


--
-- Name: orden_compra_detalles orden_compra_detalles_producto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.orden_compra_detalles
    ADD CONSTRAINT orden_compra_detalles_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES public.productos(id) ON DELETE RESTRICT;


--
-- Name: ordenes_compra ordenes_compra_proveedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ordenes_compra
    ADD CONSTRAINT ordenes_compra_proveedor_id_foreign FOREIGN KEY (proveedor_id) REFERENCES public.proveedores(id) ON DELETE RESTRICT;


--
-- Name: ordenes_compra ordenes_compra_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ordenes_compra
    ADD CONSTRAINT ordenes_compra_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- Name: productos productos_categoria_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_categoria_id_foreign FOREIGN KEY (categoria_id) REFERENCES public.categorias(id) ON DELETE RESTRICT;


--
-- Name: productos productos_proveedor_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.productos
    ADD CONSTRAINT productos_proveedor_id_foreign FOREIGN KEY (proveedor_id) REFERENCES public.proveedores(id) ON DELETE RESTRICT;


--
-- Name: users users_role_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.users
    ADD CONSTRAINT users_role_id_foreign FOREIGN KEY (role_id) REFERENCES public.roles(id) ON DELETE RESTRICT;


--
-- Name: venta_detalles venta_detalles_producto_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta_detalles
    ADD CONSTRAINT venta_detalles_producto_id_foreign FOREIGN KEY (producto_id) REFERENCES public.productos(id) ON DELETE RESTRICT;


--
-- Name: venta_detalles venta_detalles_venta_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.venta_detalles
    ADD CONSTRAINT venta_detalles_venta_id_foreign FOREIGN KEY (venta_id) REFERENCES public.ventas(id) ON DELETE CASCADE;


--
-- Name: ventas ventas_user_id_foreign; Type: FK CONSTRAINT; Schema: public; Owner: -
--

ALTER TABLE ONLY public.ventas
    ADD CONSTRAINT ventas_user_id_foreign FOREIGN KEY (user_id) REFERENCES public.users(id) ON DELETE RESTRICT;


--
-- PostgreSQL database dump complete
--

\unrestrict PPTfK3M9fveglEr257YG0scyfc1lV8dqRqHfgU7bcXpeCUfqdlAlVF7fXug85N5

