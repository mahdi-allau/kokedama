import zipfile
import os

base = os.path.dirname(os.path.abspath(__file__))
packages = ['com_kokedama', 'tpl_kokedama', 'plg_system_kokedama', 'mod_kokedama_services', 'mod_kokedama_events']

for pkg in packages:
    src_dir = os.path.join(base, 'packages', pkg)
    zip_path = os.path.join(base, 'packages', pkg + '.zip')
    with zipfile.ZipFile(zip_path, 'w', zipfile.ZIP_DEFLATED) as zf:
        for root, dirs, files in os.walk(src_dir):
            for file in files:
                file_path = os.path.join(root, file)
                arcname = os.path.relpath(file_path, src_dir)
                zf.write(file_path, arcname)
    print(f"Created {zip_path}")

# Create main package zip
main_zip = os.path.join(base, 'pkg_kokedama_v1.0.0.zip')
with zipfile.ZipFile(main_zip, 'w', zipfile.ZIP_DEFLATED) as zf:
    zf.write(os.path.join(base, 'pkg_kokedama.xml'), 'pkg_kokedama.xml')
    for pkg in packages:
        zip_path = os.path.join(base, 'packages', pkg + '.zip')
        zf.write(zip_path, os.path.join('packages', pkg + '.zip'))
print(f"Created {main_zip}")
