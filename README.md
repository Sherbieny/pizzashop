## Pizza Shop
Pizzashop is simple e-shop created with Laravel web application framework

#### Tools used

- Laravel 7.2.5
- VS code
- [Currencyconverterapi](https://www.currencyconverterapi.com/)
- [Heroku](https://www.heroku.com/)
- [PostgreSQL](https://www.postgresql.org/)
- [Pgweb](https://sosedoff.github.io/pgweb/)
- [Dillinger](https://dillinger.io/)

#### Features
- Basic cart logic (add/remove)
- Cart totals calculation
- Menu page with sample products
- Details page for each product
- User authentication (login/registration)
- Loggedin and guest cart
- Carts merge if a guest logs in
- Totals conversion from EUR to USD using real conversion rates that is updated daily
- Orders history for logged in users

#### Events
- User Login: app/Listeners/LogSuccessfulLogin
- User logout: app/Listeners/LogSuccessfulLogout

#### Links
- Website: https://limitless-forest-72332.herokuapp.com/
- DB: run in project root `heroku config:get DATABASE_URL | xargs pgweb --url`
