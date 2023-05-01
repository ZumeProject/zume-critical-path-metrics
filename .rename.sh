find ./ -type f -exec sed -i -e 's|Zume_Critical_Path|Zume_Critical_Path|g' {} \;
find ./ -type f -exec sed -i -e 's|zume_critical_path|zume_critical_path|g' {} \;
find ./ -type f -exec sed -i -e 's|zume-critical-path|zume-critical-path|g' {} \;
find ./ -type f -exec sed -i -e 's|zume_critical_path_post_type|zume_critical_path_post_type|g' {} \;
find ./ -type f -exec sed -i -e 's|Zúme Critical Path|Zúme Critical Path|g' {} \;
mv zume-critical-path.php zume-critical-path.php
rm .rename.sh
