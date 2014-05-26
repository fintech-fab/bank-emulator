<?php


?>

<div class="row container">
	<div class="col-md-12">

		<h3>Установка</h3>

		<p>
			Последняя версия SDK для шлюза находится на
			<a href="https://github.com/fintech-fab/bank-emulator-sdk" target="_blank">GitHub</a> или устанавливается
			через <a href="https://packagist.org/packages/fintech-fab/bank-emulator-sdk" target="_blank">Composer</a>.
		</p>

		<p>
			Чтобы воспользоваться нашим шлюзом, необходимо <a href="/registration">авторизоваться</a>, а затем
			<a href="<?= URL::route('ff-bank-em-term') ?>">получить терминал</a> (и тогда все платежные операции будут
			связаны с вашим аккаунтом на нашем сайте). Либо установите шлюз на свой сервер (вам понадобится Laravel,
			<a href="https://github.com/fintech-fab/bank-emulator" target="_blank">GitHub</a> и Composer). </p>

	</div>
</div>


<div class="row container">
<div class="col-md-12">

<h3>Простой вариант использования</h3>

<p>Это тип платежа "онлайн-платеж" (или эквайринг), самый распространенный способ подключения к банку для приема
	платежей. Похоже на обычные способы оплаты:</p>

<ul>
	<li>размещаете у себя на странице кнопку перехода к оплате;</li>
	<li>клиент переходит к оплате, и возвращается к вам с результатом "успешно" или "неуспешно";</li>
	<li>к вам на callbackUrl приходит запрос с подробным результатом операции;</li>
	<li>к вам на callbackEmail дублируется информация запроса на callbackUrl.</li>
</ul>

<p>Установите настройки для шлюза:</p>

<pre class="small">
<?php ob_start(); ?>
	use \FintechFab\BankEmulatorSdk\OnlineFormWidget
	use \FintechFab\BankEmulatorSdk\Config

	Config::setAll(array(
		'terminalId'    => 1,                                                     // id терминала
		'secretKey'     => 'caf6d3de33bfcc5bfc9f8a042b9f860d',                    // секретный ключ терминала
		'endpointUrl'   => '<?= URL::route('ff-bank-em-endpoint') ?>',  // url на форму шлюза для проведения онлайн-платежа
		'gatewayUrl'    => '<?= URL::route('ff-bank-em-gateway') ?>',   // url шлюза для остальных типов платежей
		'currency'      => 'RUB',                                                 // трехзначный символьный код валюты
		'callbackUrl'   => '<?= URL::route('ff-bank-em-callback') ?>',  // url вашего проекта для обработки обратных вызовов от шлюза
		'callbackEmail' => 'your.email@example.com',                              // email вашего проекта для получения обратных вызовов от шлюза
		'shopUrl'       => '<?= URL::route('ff-bank-em-shop') ?>',      // url платежной страницы в вашем проекте
	));
	<?php highlight_string("<?php\n" . ob_get_clean()) ?>
</pre>

<p>Выведите на странице вашего проекта html-код кнопки перехода к оплате:</p>
<pre class="small">
<?php ob_start(); ?>
	OnlineFormWidget::render(
		12345,                  // номер заказа в вашей системе
		123.45,                 // сумма заказа
		'Example',              // название заказа (опционально)
		'Example Online Order'  // описание заказа (опционально)
	);
	<?php highlight_string("<?php\n" . ob_get_clean()) ?>
</pre>

<p>Нажмите на кнопку. Попробовать можете <a href="<?= URL::route('ff-bank-em-shop') ?>">здесь и сейчас</a>.</p>

<p>Важно! тип 'endpoint' это генерация данных для запроса, но на шлюзе выполняется операция 'payment' (результат именно
	этой операции будет отправлен на callbackUrl и callbackEmail)</p>

<p>Теперь принимайте результат выполнения платежной операции на callbackUrl.</p>

<h3>Вариант посложнее</h3>

<p>Вот так:</p>

<pre class="small">
<?php ob_start(); ?>
	use \FintechFab\BankEmulatorSdk\Gateway;

	$gateway = Gateway::newInstance();
	$params = array(
			'orderId'     => 1,
			'orderName'   => 'Example Item',
			'orderDesc'   => 'Perfect Order By YourShop.Com',
			'orderAmount' => 123.45,
	);
	$payFormFields = $gateway->endpoint();
	<?php highlight_string("<?php\n" . ob_get_clean()) ?>
</pre>
<p>вы получите список полей и значений для того, чтобы самостоятельно вывести форму онлайн-платежа для вашего клиента и
	отправить его на форму endpoint.</p>


<h3>Другие типы платежей</h3>

<p>Все другие типы платежей пригодятся тем, кто самостоятельно принимает от клиентов данные банковских карт, и
	предлагает клиентам собственную платежную страницу. В таком случае с помощью шлюза, возможно проводить "прямые"
	платежи с карт клиентов без участия самих клиентов (за исключением того, когда потребуется пройти процедуру
	авторизации платежа).</p>

<p>Все типы платежей работают у нас и их можно опробовать с помощью отладочных форм
	<a href="<?= URL::route('ff-bank-em-term') ?>">здесь</a>. Справочник по типам платежей, входящим и исходящим
	параметрам запросов - <a href="<?= URL::route('ff-bank-em-docs') ?>">здесь</a>. Класс Gateway из пакета
	<a href="https://github.com/fintech-fab/bank-emulator-sdk" target="_blank">Bank Emulator SDK</a> предоставляет все
	необходимые методы, чтобы упростить выполнение запросов к шлюзу и разбор ответов от шлюза. Вопросы задавайте на
	сервисе GitHub, если они у вас возникнут.</p>

<h3>Описание методов Gateway SDK Api</h3>

<table class="table table-striped table-hover">


	<tr>
		<th>Метод</th>
		<th>Параметры</th>
		<th>Описание</th>
		<th>Результат</th>
	</tr>


	<tr>
		<td>endpoint</td>
		<td>
					<pre class="small">
orderId      id заказа в интернет-магазине
orderAmount  сумма (стоимость) заказа к оплате
[orderName]  название заказа в интернет-магазине
[orderDesc]  описание заказа
					</pre>
		</td>
		<td>Возвращает список полей (ключ - значение) для генерации html-кода платежной формы</td>
		<td>&mdash;</td>
	</tr>


	<tr>
		<td>sale</td>
		<td>
					<pre class="small">
orderId       id заказа в интернет-магазине
orderAmount   сумма (стоимость) заказа к оплате
cardNumber    номер карты для оплаты заказа
expiredYear   год окончания действия карты
expiredMonth  месяц окончания действия карты
cvcCode       cvc/cvv код карты
[paymentTo]   номер счета/карты получателя платежа
              в случае перевода средств с карты на карту
[orderName]   название заказа в интернет-магазине
[orderDesc]   описание заказа
					</pre>
		</td>
		<td>Выполняет запрос к шлюзу по типу sale (Продажа)</td>
		<td>
					<pre class="small">
Методы
  getResultStatus статус выполнения операции
  getAuthUrl      url для перенаправления плательщика в случае статуса auth
  getResultRRN    код RRN для запроса на отмену платежа
  getResultIRN    код IRN для запроса на отмену платежа
Статусы
  success    платеж выполнен успешно
  auth       требуется авторизация платежа
					</pre>
		</td>
	</tr>


	<tr>
		<td>auth</td>
		<td>
					<pre class="small">
orderId       id заказа в интернет-магазине
orderAmount   сумма (стоимость) заказа к оплате
cardNumber    номер карты для оплаты заказа
expiredYear   год окончания действия карты
expiredMonth  месяц окончания действия карты
cvcCode       cvc/cvv код карты
[paymentTo]   номер счета/карты получателя платежа
              в случае перевода средств с карты на карту
[orderName]   название заказа в интернет-магазине
[orderDesc]   описание заказа
					</pre>
		</td>
		<td>Выполняет предварительный запрос к шлюзу по типу auth (Авторизационный)</td>
		<td>
					<pre class="small">
Методы
  getResultStatus статус выполнения операции
  getAuthUrl      url для перенаправления плательщика в случае статуса auth
  getResultRRN    код RRN для запроса на завершение/отмену платежа
  getResultIRN    код IRN для запроса на завершение/отмену платежа
Статусы
  success    платеж доступен для проведения
  auth       требуется авторизация платежа
					</pre>
		</td>
	</tr>

	<tr>
		<td>complete</td>
		<td>
					<pre class="small">
orderId       id заказа в интернет-магазине
orderAmount   сумма (стоимость) заказа к оплате
rrn           результат метода getResultRRN после успешного запроса auth
irn           результат метода getResultIRN после успешного запроса auth
					</pre>
		</td>
		<td>Исполняет запрос на завершение платежа по типу complete (Завершение продажи)</td>
		<td>
					<pre class="small">
Методы
  getResultStatus статус выполнения операции
Статусы
  success    платеж выполнен успешно
					</pre>
		</td>
	</tr>


	<tr>
		<td>refund</td>
		<td>
					<pre class="small">
orderId       id заказа в интернет-магазине
orderAmount   сумма (стоимость) заказа к оплате
rrn           результат метода getResultRRN после успешного запроса auth
irn           результат метода getResultIRN после успешного запроса auth
					</pre>
		</td>
		<td>Исполняет отмену платежа по типу refund (Отмена плтаежа)</td>
		<td>
					<pre class="small">
Методы
  getResultStatus статус выполнения операции
Статусы
  success    отмена выполнена успешно
					</pre>
		</td>
	</tr>

	<tr>
		<td>callback</td>
		<td>
					<pre class="small">
[array]     данные от шлюза, отправленные POST-запросом на callbackUrl
            по умолчанию - значение глобальной переменной $_POST
					</pre>
		</td>
		<td>Выполняет обработку POST-запроса от шлюза на callbackUrl</td>
		<td>
					<pre class="small">
Методы
  getResultStatus      статус выполнения операции
  getResultType        тип выполненной операции (sale|auth|refund|complete|payment)
  getResultOrderId     id заказа, по которому выполнена операция
  getResultAmount      сумма заказа, на которую выполенна операция
  getResultIRN         код операции RRN
  getResultIRN         код операции IRN
  getResultRC          код результата операции
  getResultTerminalId  id терминала, по которому выполнена операция
  getError             текст ошибки, если результатом операции является ошибка
					</pre>
		</td>
	</tr>

</table>

<h3>Обработка ошибок Gateway SDK Api</h3>

<table class="table table-striped table-hover">
	<tr>
		<th>Метод</th>
		<th>Описание</th>
	</tr>
	<tr>
		<td>getError</td>
		<td>Сообщение об ошибки, если результатом выполнения операции является ошибка</td>
	</tr>
	<tr>
		<td>getResultMessage</td>
		<td>Сообщение об ошибке процесса, если результатом выполнения операции является ошибка процессинга платежей</td>
	</tr>
	<tr>
		<td>getErrorType</td>
		<td>
			Тип ошибки, если результатом выполнения операции является ошибка
					<pre class="small">
Gateway::C_ERROR_HTTP          Ошибка http запроса
Gateway::C_ERROR_GATEWAY       Системная ошибка шлюза
Gateway::C_ERROR_PROCESSING    Ошибка процессинга платежей
Gateway::C_ERROR_PARAMS        Ошибка в параметрах запроса
					</pre>
		</td>
	</tr>
</table>

</div>
</div>
