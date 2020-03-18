# User

[![Build Status](https://travis-ci.com/pluf/user.svg?branch=master)](https://travis-ci.com/pluf/user)
[![codecov](https://codecov.io/gh/pluf/user/branch/master/graph/badge.svg)](https://codecov.io/gh/pluf/user)
[![Coverage Status](https://coveralls.io/repos/github/pluf/user/badge.svg)](https://coveralls.io/github/pluf/user)
[![Maintainability](https://api.codeclimate.com/v1/badges/9e1457dbf2f0bcc8b953/maintainability)](https://codeclimate.com/github/pluf/user/maintainability)
[![Test Coverage](https://api.codeclimate.com/v1/badges/9e1457dbf2f0bcc8b953/test_coverage)](https://codeclimate.com/github/pluf/user/test_coverage)



این پروژه یک ماژول برای سرورهای pluf است که امکانات مدیریت پروفایل‌ها و حساب‌های کاربری را فراهم می‌کند.

این ماژول به ماژول‌های زیر وابستگی دارد:

- pluf/user
- pluf/collection

از نکات مهم این ماژول این است که ساختار پروفایل ثابت نیست و می‌تواند در سیستم‌های مختلف متفاوت باشد
پروفایل در اینجا به صورت یک نگاشت کلید-مقدار است که هر زوج کلید-مقداری را می‌توان در آن ذخیره کرد.

## Update from V3

### DB

#### Copy table `user` to `user_accounts` and `user_profiles`

In `user_accounts` keep following attributes:

- id
- login
- date_joined
- last_login
- active

Add the following:

- deleted

#### add `user_emails` table

#### rename `user_groups`

#### rename `user_messages`