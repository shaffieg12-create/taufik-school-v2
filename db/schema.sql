PRAGMA foreign_keys = ON;

-- USERS
CREATE TABLE users (
    id INTEGER PRIMARY KEY,
    name TEXT,
    email TEXT UNIQUE,
    password TEXT,
    role INTEGER DEFAULT 3,
    house_id INTEGER DEFAULT NULL,
    created DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- HOUSES
CREATE TABLE houses (
    id INTEGER PRIMARY KEY,
    name TEXT UNIQUE,
    color TEXT,
    logo TEXT,
    patron_id INTEGER REFERENCES users(id),
    motto TEXT,
    date_created DATE DEFAULT CURRENT_DATE
);

-- STUDENTS
CREATE TABLE students (
    id INTEGER PRIMARY KEY,
    code TEXT UNIQUE,
    first_name TEXT,
    last_name TEXT,
    gender TEXT,
    dob DATE,
    class_id INTEGER,
    boarding INTEGER DEFAULT 0,
    quran_hifz INTEGER DEFAULT 0,
    rfid_card TEXT,
    parent_phone TEXT,
    parent_email TEXT,
    avatar TEXT,
    allergies TEXT,
    status INTEGER DEFAULT 1
);

-- HOUSE MEMBERSHIP
CREATE TABLE student_house (
    id INTEGER PRIMARY KEY,
    student_id INTEGER REFERENCES students(id) ON DELETE CASCADE,
    house_id INTEGER REFERENCES houses(id) ON DELETE CASCADE,
    year TEXT,
    role TEXT,
    UNIQUE(student_id, year)
);

-- HOUSE POINTS
CREATE TABLE house_points (
    id INTEGER PRIMARY KEY,
    house_id INTEGER REFERENCES houses(id),
    date DATE,
    points INTEGER,
    reason TEXT,
    teacher_id INTEGER REFERENCES users(id),
    synced INTEGER DEFAULT 1
);

-- FEES
CREATE TABLE fee_invoices (
    id INTEGER PRIMARY KEY,
    student_id INTEGER REFERENCES students(id),
    term TEXT,
    total INTEGER,
    paid INTEGER DEFAULT 0,
    balance INTEGER GENERATED ALWAYS AS (total - paid) STORED,
    due_date DATE
);

-- ATTENDANCE
CREATE TABLE attendance (
    id INTEGER PRIMARY KEY,
    student_id INTEGER REFERENCES students(id),
    date DATE,
    time TEXT,
    direction TEXT,
    reader_id TEXT,
    synced INTEGER DEFAULT 1
);
