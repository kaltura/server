Import-Module -name $args[0]
Convert-Media -Input $args[1] -PresetFileName $args[2] -ErrorVariable err

echo $err

exit $err.count
