import re

files_to_fix = [
    r'C:\Users\mahdi\Desktop\kokedma_simona\app\templates\header.php',
    r'C:\Users\mahdi\Desktop\kokedma_simona\app\admin\index.php',
    r'C:\Users\mahdi\Desktop\kokedma_simona\app\admin\_layout.php',
]

for path in files_to_fix:
    with open(path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Sostituisci APP_URL con APP_BASE SOLO per i riferimenti a /assets/
    old = 'APP_URL; ?>/assets/'
    new = 'APP_BASE; ?>/assets/'
    
    if old in content:
        content = content.replace(old, new)
        with open(path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f'Fixato: {path}')
    else:
        print(f'Nessun cambiamento: {path}')

print('Done!')
