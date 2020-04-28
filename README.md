# lightuna

lightuna는 누구나 쉽게 웹호스팅을 통해 서비스할 수 있는 스레드 플로팅 방식의 게시판이다.
이를 위해 프레임워크나 라이브러리를 사용하지 않고 디렉토리를 FTP만으로 업로드하여 사용할 수 있도록 개발되었다.

## 요구사항

 * PHP 7.3
 * MariaDB 10.3

## 설정

`/config/profile.php` 파일을 통해 설정한다.
`LIGHTUNA_ENV` 환경변수를 설정하면 `/config/profile.{$LIGHTUNA_ENV}.php` 파일을 로드한다.

설정파일에는 `boards` 배열이 존재하며 해당 배열 내에 게시판이 설정되어야 해당 게시판으로 접근할 수 있다.
`boards` 하위의 `__default__` 배열은 게시판 설정에 대한 기본값을 제공하며 해당 배열의 키값은 삭제해서는 안된다.
`boards` 하위에 신규 게시판을 추가할 경우 `uid`, `name` 키를 반드시 설정해야한다.
이외에 `__default__` 배열과 겹치는 키값을 설정하면 해당 게시판 전용으로 해당 설정을 덮어쓴다.

`/config/init.sql` 파일에 데이터베이스 설정을 위한 DDL이 존재한다.
`https://your-domain.com/lightuna/install.php`로 접속하면 해당 DDL을 실행한다.
데이터베이스 셋업이 완료되면 `install.php`는 삭제한다.
