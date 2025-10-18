-- =========================================================
--  Taufik Junior School & Qur'an Centre  –  SQLite schema
--  Houses / Clubs / Societies included
-- =========================================================
PRAGMA foreign_keys = ON;

-- 1.  USERS  (roles: 1=Super 2=Admin 3=Teacher 4=Nurse 5=Accountant 6=Parent)
CREATE TABLE users (
    id       INTEGER PRIMARY KEY,
    name     TEXT,
    email    TEXT UNIQUE,
    password TEXT,               -- bcrypt hash
    role     INTEGER DEFAULT 3,
    house_id INTEGER DEFAULT NULL,  -- patron of a house/club
    created  DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2.  CLASSES
CREATE TABLE classes (
    id          INTEGER PRIMARY KEY,
    name        TEXT UNIQUE,
    section     TEXT,   -- nursery, primary, quran
    day_fee     INTEGER,
    boarding_fee INTEGER,
    quran_fee   INTEGER
);

-- 3.  STUDENTS
CREATE TABLE students (
    id            INTEGER PRIMARY KEY,
    code          TEXT UNIQUE,          -- TJS25-0001
    first_name    TEXT,
    last_name     TEXT,
    gender        TEXT,
    dob           DATE,
    class_id      INTEGER REFERENCES classes(id),
    boarding      INTEGER DEFAULT 0,
    quran_hifz    INTEGER DEFAULT 0,
    rfid_card     TEXT,
    parent_phone  TEXT,
    parent_email  TEXT,
    avatar        TEXT,
    allergies     TEXT,
    status        INTEGER DEFAULT 1
);

-- 4.  FEES
CREATE TABLE fee_templates (
    id      INTEGER PRIMARY KEY,
    class_id INTEGER REFERENCES classes(id),
    items   TEXT               -- JSON {"tuition":450000,"uniform":80000}
);

CREATE TABLE fee_invoices (
    id        INTEGER PRIMARY KEY,
    student_id INTEGER REFERENCES students(id),
    term      TEXT,               -- 2025-T3
    total     INTEGER,
    paid      INTEGER DEFAULT 0,
    balance   INTEGER GENERATED ALWAYS AS (total - paid) STORED,
    due_date  DATE
);

CREATE TABLE fee_payments (
    id         INTEGER PRIMARY KEY,
    invoice_id INTEGER REFERENCES fee_invoices(id),
    amount     INTEGER,
    channel    TEXT,               -- cash, momo, bank
    ref        TEXT,
    date       DATETIME DEFAULT CURRENT_TIMESTAMP,
    reconciled INTEGER DEFAULT 1
);

-- 5.  ATTENDANCE
CREATE TABLE attendance (
    id         INTEGER PRIMARY KEY,
    student_id INTEGER REFERENCES students(id),
    date       DATE,
    time       TEXT,
    direction  TEXT,      -- IN / OUT
    reader_id  TEXT,      -- gate, dorm, library
    synced     INTEGER DEFAULT 1
);

-- 6.  HOUSES / CLUBS / SOCIETIES
CREATE TABLE houses (
    id          INTEGER PRIMARY KEY,
    name        TEXT UNIQUE,
    color       TEXT,               -- hex
    logo        TEXT,
    patron_id   INTEGER REFERENCES users(id),
    motto       TEXT,
    date_created DATE DEFAULT CURRENT_DATE
);

CREATE TABLE student_house (
    id         INTEGER PRIMARY KEY,
    student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
    house_id   INTEGER REFERENCES houses(id)   ON DELETE CASCADE,
    year       TEXT,
    role       TEXT,      -- Member, Captain, Vice, Prefect
    UNIQUE(student_id, year)
);

CREATE TABLE house_points (
    id       INTEGER PRIMARY KEY,
    house_id INTEGER REFERENCES houses(id),
    date     DATE,
    points   INTEGER,    -- positive or negative
    reason   TEXT,
    teacher_id INTEGER REFERENCES users(id),
    synced   INTEGER DEFAULT 1
);

-- 7.  REQUIREMENTS (uniform, stationery, boarding items)
CREATE TABLE requirement_items (
    id       INTEGER PRIMARY KEY,
    name     TEXT,
    category TEXT   -- uniform, stationery, boarding, quran
);

CREATE TABLE student_requirements (
    id          INTEGER PRIMARY KEY,
    student_id  INTEGER REFERENCES students(id),
    item_id     INTEGER REFERENCES requirement_items(id),
    qty_needed  INTEGER,
    qty_brought INTEGER,
    date        DATE DEFAULT CURRENT_DATE
);

-- 8.  DISCIPLINE & COUNSELLING
CREATE TABLE discipline_cases (
    id          INTEGER PRIMARY KEY,
    student_id  INTEGER REFERENCES students(id),
    date        DATE,
    category    TEXT,
    description TEXT,
    action      TEXT,
    teacher_id  INTEGER REFERENCES users(id),
    parent_notified INTEGER DEFAULT 0,
    points      INTEGER DEFAULT 0
);

-- 9.  DORMITORY / BEDS
CREATE TABLE dorm_beds (
    id        INTEGER PRIMARY KEY,
    dorm      TEXT,   -- A, B, C …
    bed_no    TEXT,
    student_id INTEGER UNIQUE REFERENCES students(id) ON DELETE SET NULL
);

-- 10.  LIBRARY
CREATE TABLE library_books (
    id        INTEGER PRIMARY KEY,
    isbn      TEXT,
    title     TEXT,
    author    TEXT,
    cover     TEXT,
    copies    INTEGER DEFAULT 1
);

CREATE TABLE book_loans (
    id        INTEGER PRIMARY KEY,
    book_id   INTEGER REFERENCES library_books(id),
    student_id INTEGER REFERENCES students(id),
    date_out  DATE,
    date_due  DATE,
    date_in   DATE,
    fine      INTEGER DEFAULT 0
);

-- 11.  MEDICAL
CREATE TABLE medical_visits (
    id          INTEGER PRIMARY KEY,
    student_id  INTEGER REFERENCES students(id),
    date        DATE,
    symptoms    TEXT,
    treatment   TEXT,
    dosage      TEXT,
    doctor      TEXT,
    nurse_id    INTEGER REFERENCES users(id)
);

CREATE TABLE drug_stock (
    id       INTEGER PRIMARY KEY,
    name     TEXT UNIQUE,
    qty      INTEGER,
    unit     TEXT
);

-- 12.  STAFF PAYROLL
CREATE TABLE staff (
    id            INTEGER PRIMARY KEY,
    user_id       INTEGER UNIQUE REFERENCES users(id),
    tin           TEXT,
    nssf_no       TEXT,
    bank_name     TEXT,
    bank_account  TEXT,
    basic         INTEGER,
    allowances    TEXT  -- JSON
);

CREATE TABLE payroll (
    id         INTEGER PRIMARY KEY,
    staff_id   INTEGER REFERENCES staff(id),
    month      TEXT,  -- 2025-03
    gross      INTEGER,
    nssf       INTEGER,
    paye       INTEGER,
    net        INTEGER,
    payslip    TEXT  -- file name
);

-- 13.  PETTY CASH
CREATE TABLE petty_cash (
    id         INTEGER PRIMARY KEY,
    dept       TEXT,
    requester  INTEGER REFERENCES users(id),
    amount     INTEGER,
    reason     TEXT,
    approved   INTEGER DEFAULT 0,
    paid       INTEGER DEFAULT 0,
    balance    INTEGER,
    date       DATE DEFAULT CURRENT_DATE
);

-- 14.  EVENTS & COMPETITIONS
CREATE TABLE events (
    id          INTEGER PRIMARY KEY,
    title       TEXT,
    venue       TEXT,
    edate       DATE,
    etime       TEXT,
    house_comp  INTEGER DEFAULT 0,
    result      TEXT  -- JSON winner array
);

-- 15.  COMMUNICATION (SMS/EMAIL LOG)
CREATE TABLE comm_log (
    id        INTEGER PRIMARY KEY,
    recipients TEXT,
    message   TEXT,
    type      TEXT,  -- sms, email
    date      DATETIME DEFAULT CURRENT_TIMESTAMP,
    status    TEXT
);

-- 16.  SYSTEM LOCKS
CREATE TABLE config (
    key TEXT UNIQUE PRIMARY KEY,
    value TEXT
);
