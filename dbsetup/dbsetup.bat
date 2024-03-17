:: Setup script to run mysql and create the neccessary databases and tables.
:: Note this expects 'mysql' to be avaliable on path as a command.
:: This does assume a need in varchar length for sertain columns.
::
:: Finally it creates an account on the admin-page
::
mysql -u root < dbsetup.sql