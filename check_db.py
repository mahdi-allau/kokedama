import sqlite3

DB_PATH = r'C:/Users/mahdi/Desktop/kokedma_simona/app/database.sqlite'
conn = sqlite3.connect(DB_PATH)
cursor = conn.cursor()

cursor.execute("SELECT name FROM sqlite_master WHERE type='table';")
tables = cursor.fetchall()
print(f"Tabelle trovate: {len(tables)}")
for t in tables:
    print(f"  - {t[0]}")

print("\nContenuto tabelle:")
for table in [t[0] for t in tables if not t[0].startswith('sqlite_')]:
    cursor.execute(f"SELECT COUNT(*) FROM {table}")
    count = cursor.fetchone()[0]
    print(f"  {table}: {count} record")

conn.close()
print("\nDatabase verificato con successo!")
