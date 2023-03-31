<!--

1. 選択できるアクションに「回復する」を追加して下さい
　 ・回復は以下の仕様を満たして下さい
　 ・10-100の間でランダムな数値回復する
　 ・回復は3回までしか使えない
　 ・初期のHPを超えてHPを増やすことはできない

2. 空が飛べるモンスターを追加して下さい
　 ・空が飛べるモンスターは以下を満たすクラスとして定義して下さい
　 ・Monsterクラスを継承
　 ・attack()メソッドを実行した時に、1/3の確率で「空からの体当たり攻撃」を行う
　 ・「空からの体当たり攻撃」を行った時には、通常の1.2倍のダメージを与える
　 ・「空からの体当たり攻撃」を行った時には、モンスターのHPが20減る

3. モンスターの代わりにランダムで「神様」を登場させて下さい
　 ・神様が登場した時には、以下のアクションを選択できます
　 ・「回復してもらう」
　 ・HPが初期値まで回復する
　 ・「強くしてもらう」
　 ・最大攻撃力と最小攻撃力が20上がる
　 ・「丈夫にしてもらう」
　 ・最大HPが2倍になる

4. 「勇者」でゲームを行うか「魔法使い」でゲームを行うか選べるようにして下さい
　 ・魔法使いは以下を満たすクラスとして定義して下さい
・Humanクラスを継承
・プロパティにmpを持つ
・mpはインスタンス生成時に50-100の間でランダムに決定
・attack()メソッドを実行した時に、1/3の確率で魔法攻撃を行う
・魔法攻撃を行った時には、mpが10減る
・mpが0の時には、必ず通常攻撃になる
・魔法攻撃は、ランダムで通常攻撃の50%-200%のダメージになる
・モンスターが「空が飛べるモンスター」の場合、魔法攻撃のダメージは必ず1.5倍になる
・「効果が抜群」の表示を行う

5. ボスモンスターを追加して下さい
　 ・ボスを倒したらゲームクリアにして下さい
　 ・ボスは5体目以降でランダムに登場するようにして下さい
　 ・ボスは以下を満たすクラスとして定義して下さい
　 ・Monsterクラスを継承
　 ・攻撃の最小値は固定で50
　 ・攻撃の最大値は固定で80
・HPはボス登場までに倒されたモンスターの数に応じて変化する
・500 + （倒されたモンスターの数 * 10）　をHPとする

-->

<?php

//=======================================
//ログとセッション
//=======================================

ini_set('log_errors','on');
ini_set('error_log','php.log');
session_start();

//モンスターの変数を配列に
$monsters = array();


//=======================================
//クラス
//=======================================

//性別クラス
class Sex{
  //クラス定数で
  const MAN = 1;
  const WOMAN = 2;
  const FREE = 3;
}

//神様クラス
class God{
  //プロパティ
  protected $name;
  protected $img;
  //コンストラクタ
  public function __construct($name,$img){
    $this->name     = $name;
    $this->img     = $img;
  }
  //ゲッター
  public function getGodName(){
    return $this->name;
  }
  public function getGodImg(){
    return $this->img;
  }
}

//抽象クラス
abstract class Creature{
  //プロパティ
  protected $name;
  protected $hp;
  protected $attackMin;
  protected $attackMax;
  abstract public function sayCry();

  //セッター
  public function setName($str){
    $this->name = $str;
  }
  public function setHp($num){
    $this->hp = $num;
  }

  //ゲッター
  public function getName(){
    return $this->name;
  }
  public function getHp(){
    return $this->hp;
  }

  //攻撃メソッド
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin,$this->attackMax);
    History::set($this->getName().'の攻撃！');
    //クリティカルヒット
    if(!mt_rand(0,9)){
      $attackPoint *= 1.5;
      $attackPoint = (int)$attackPoint;
      History::set('クリティカルヒット！');
    }
    $targetObj->setHp($targetObj->getHp() - $attackPoint);
    History::set($attackPoint.'のダメージ！');
  }
}

//人間クラス
class Human extends Creature{
  //プロパティ
  protected $sex;
  protected $maxHp;
  protected $mp;
  //コンストラクタ
  public function __construct($name,$sex,$hp,$maxHp,$mp,$attackMin,$attackMax){
    $this->name      = $name;
    $this->sex       = $sex;
    $this->hp        = $hp;
    $this->maxHp     = $maxHp;
    $this->mp        = $mp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  //セッター
  public function setSex(){
    $this->sex = $sex;
  }
  public function setMaxHp($num){
    $this->maxHp = $num;
  }
  public function setAttackMin($num){
    $this->attackMin = $num;
  }
  public function setAttackMax($num){
    $this->attackMax = $num;
  }

  //ゲッター
  public function getSex(){
    return $this->sex;
  }
  public function getMaxHp(){
    return $this->maxHp;
  }
  public function getMp(){
    return $this->mp;
  }
  public function getAttackMin(){
    return $this->attackMin;
  }
  public function getAttackMax(){
    return $this->attackMax;
  }
  //メソッド
  public function sayCry(){
    //性別によって叫び方を変える
    switch($this->sex){

      case Sex::MAN :
        History::set('ぐっ...！');
        break;

      case Sex::WOMAN :
        History::set('きゃぁぁぁ！');
        break;

      case Sex::FREE :
        History::set('おほほほ！');
        break;
    }
  }
}

//魔法使いクラス(Humanクラスを継承)
class Wizard extends Human{
  //コンストラクタ
  public function __construct($name,$sex,$hp,$maxHp,$mp,$attackMin,$attackMax){
    $this->name      = $name;
    $this->sex       = $sex;
    $this->hp        = $hp;
    $this->maxHp     = $maxHp;
    $this->mp        = $mp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }

  //セッター
  public function setMp($num){
    $this->mp = $num;
  }

  //メソッド
  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin,$this->attackMax);
    //MPがある場合3分の１の確率で魔法攻撃
      if(!mt_rand(0,2)){
        if($this->getMp() >= 10){
          //MPを１０消費する
          $this->setMp($this->getMp() - 10);

          //相手が空を飛ぶモンスターの場合
          if($targetObj instanceof FlyMonster){
            //攻撃力は1.5倍に
            $attackPoint *= 1.5;
            $attackPoint = (int)$attackPoint;
            History::set($this->getName().'はライトニングを唱えた！');
            History::set('こうかは ばつぐんだ！');

          }else{

            //５０％〜２００％の範囲で、ランダムで攻撃力変化
            $attackPoint = $attackPoint * (mt_rand(50,200) / 100 );
            $attackPoint = (int)$attackPoint;
            History::set($this->getName().'はライトニングを唱えた！');
          }

          $targetObj->setHp($targetObj->getHp() - $attackPoint);
          History::set($attackPoint.'のダメージ！');
        }
    }else{
      parent::attack($targetObj);
    }
  }
}


//モンスタークラス
class Monster extends Creature{
  //プロパティ
  protected $img;
  //コンストラクタ
  public function __construct($name,$hp,$img,$attackMin,$attackMax){
    $this->name      = $name;
    $this->hp        = $hp;
    $this->img       = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  //ゲッター
  public function getImg(){
    return $this->img;
  }
  //メソッド
  public function sayCry(){
    History::set('グギャァァ！');
  }
}

//魔法を使うモンスタークラス
class MagicMonster extends Monster{
  //プロパティ
  protected $magicAttack;
  //コンストラクタ
  public function __construct($name,$hp,$img,$attackMin,$attackMax,$magicAttack){
    parent::__construct($name,$hp,$img,$attackMin,$attackMax);
    $this->magicAttack = $magicAttack;
  }
  //ゲッター
  public function getMagicAttack(){
    return $this->magicAttack;
  }
  //攻撃メソッド
  public function attack($targetObj){
    //5分の１の確率で魔法攻撃
    if(!mt_rand(0,4)){
      $attackPoint = $this->magicAttack;
      History::set($this->getName().'はカイザーフェニックスを唱えた！');
      $targetObj->setHp($targetObj->getHp() - $attackPoint);
      History::set($attackPoint.'のダメージ！');
    }else{
      parent::attack($targetObj);
    }
  }
}

//空を飛ぶモンスタークラス
class FlyMonster extends Monster{
  //プロパティ
  protected $flyAttack;
  //コンストラクタ
  public function __construct($name,$hp,$img,$attackMin,$attackMax,$flyAttack){
    parent::__construct($name,$hp,$img,$attackMin,$attackMax);
    $this->flyAttack = $flyAttack;
  }
  //ゲッター
  public function getFlyAttack(){
    return $this->flyAttack;
  }
  //攻撃メソッド
  public function attack($targetObj){
    $attackPoint = $this->flyAttack;
    //3分の１の確率で空中攻撃
    if(!mt_rand(0,2)){
      $attackPoint = $attackPoint * 1.2;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'は空に飛び上がり体当たりしてきた！');
      $targetObj->setHp($targetObj->getHp() - $attackPoint);
      History::set($attackPoint.'のダメージ！');
      $this->setHp($this->getHp() - 20);
      History::set($this->getName().'も20のダメージを受けた！');
      if($this->getHp() < 0){
        History::set($this->getName().'は息たえた！');
        createMonster();
      }
    }else{
      parent::attack($targetObj);
    }
  }
}

//ボスモンスター
class Boss extends Monster{
  //コンストラクタ
  public function __construct($name,$hp,$img,$attackMin,$attackMax){
    $this->name      = $name;
    $this->hp        = $hp;
    $this->img       = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
}


//履歴クラスのインターフェース
interface HistoryInterface{
  public static function set($str);
  public static function clear();
}

//履歴クラス
class History implements HistoryInterface{

  public static function set($str){
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    $_SESSION['history'] = $str.'<br>'.$_SESSION['history'];
  }
  public static function clear(){
    unset($_SESSION['history']);
  }
}

//=======================================
//インスタンス
//=======================================

$god = new God('神さま','img/img1.png');
$monsters[] = new Monster('フランケン',50,'img/monster01.png',20,30);
$monsters[] = new Monster('ドラキュリー',55,'img/monster03.png',30,35);
$monsters[] = new Monster('ゾンビーハンド',60,'img/monster06.png',45,55);
$monsters[] = new Monster('スカルヘッド',65,'img/monster05.png',50,55);
$monsters[] = new Monster('ポイズンスライム',80,'img/monster11.png',35,60);
$monsters[] = new Monster('ゴーレム',100,'img/monster10.png',40,75);
$monsters[] = new Monster('オークロード',120,'img/monster12.png',50,75);
$monsters[] = new Monster('フェンリル',200,'img/monster09.png',60,80);
$monsters[] = new MagicMonster('カイザードラゴン',220,'img/monster13.png',70,100,mt_rand(75,90));
$monsters[] = new FlyMonster('ジャイアントバット',150,'img/img2.png',25,50,mt_rand(25,50));
$monsters[] = new Boss('極・北京ダック(ボス)',500,'img/img21.jpeg',50,80);


//=======================================
//関数
//=======================================

//人間生成
function createHuman(){
  global $human;
  $_SESSION['human'] = $human;
}

//神様生成
function createGod(){
  global $god;
  global $godFlg;

  $_SESSION['god'] = $god;
  History::set('ほっほっほっ。ねがいを1つ叶えてやるぞ');
  History::set($_SESSION['god']->getGodName().'があらわれた！');
  $godFlg = true;
}

//モンスター生成
function createMonster(){
  global $monsters;
  global $godFlg;

  if(empty($godFlg)){

    //討伐数が５以上の場合はボスもランダムで
    if($_SESSION['knockDownCount'] >= 7){
      $monster = $monsters[mt_rand(0,10)];
      History::set($monster->getName().'があらわれた！');
      $_SESSION['monster'] = $monster;

      //ボスの場合はHPを動的に変化
      if($monster instanceof Boss){
        $_SESSION['boss_flg'] = true;
         History::set('よくここまで来たな'.$_SESSION['human']->getName().'よ！');
        $_SESSION['monster']->setHp($_SESSION['monster']->getHp()+ $_SESSION['knockDownCount'] * 10 );
      }
    }else{
      $monster = $monsters[mt_rand(0,9)];
      History::set($monster->getName().'があらわれた！');
      $_SESSION['monster'] = $monster;
    }

  }

}

//初期化
function init(){
  $_SESSION['knockDownCount'] = 0;
  History::clear();
  History::set('初期化します');
  $_SESSION['healCount'] = 0;
  unset($_SESSION['boss_flg']);
  unset($_SESSION['godFlg']);
  createMonster();
  createHuman();
}

//ゲームオーバー
function gameOver(){
  $_SESSION =array();
}

//神さま回復
function godHeal(){
  $_SESSION['human']->setHp($_SESSION['human']->getMaxHp());
  History::set($_SESSION['human']->getName().'のHPが全回復した！');
}
//攻撃強化
function godPower(){
  $_SESSION['human']->setAttackMin($_SESSION['human']->getAttackMin() + 20);
  $_SESSION['human']->setAttackMax($_SESSION['human']->getAttackMax() + 20);
  History::set($_SESSION['human']->getName().'の攻撃力が20あがった！');

}
//HP２倍
function godDefense(){
  $_SESSION['human']->setMaxHp($_SESSION['human']->getMaxHp() * 2);
  History::set($_SESSION['human']->getName().'の最大HPが２倍になった！');

}


//=======================================
//実際の処理
//=======================================


//POSTがあった場合
if(!empty($_POST)){
  error_log(print_r($_POST,true));
  error_log(print_r($_SESSION,true));


  //POSTを変数に
  $titleFlg  = (!empty($_POST['title']))? true : false;
  $startFlg  = (!empty($_POST['start-hero']) || !empty($_POST['start-wizard']))?   true : false;
  $attackFlg = (!empty($_POST['attack']))?  true : false;
  $escapeFlg = (!empty($_POST['escape']))?  true : false;
  $healFlg   = (!empty($_POST['heal']))?    true : false;

  $gameOverFlg  = false;
  $gameClearFlg = false;
  $godFlg = false;

  //職業によって生成するインスタンスを分ける
  if(!empty($_POST['start-hero'])){
    $human = new Human('勇者A',Sex::MAN,500,500,0,40,120);

  }
  if(!empty($_POST['start-wizard'])){
    $human = new Wizard('魔法少女',Sex::WOMAN,500,500,mt_rand(50,100),40,100);
   }


  //スタート
  if($startFlg){

    History::set('ゲームスタート');
    init();
  }else{
    //攻撃
    if($attackFlg){
      //自分から攻撃
      $_SESSION['human']->attack($_SESSION['monster']);

      //モンスターのHPがある場合はモンスターの攻撃
      if($_SESSION['monster']->getHp() > 0 ){
        $_SESSION['monster']->attack($_SESSION['human']);
        //プレイヤーのHPが０以下の場合はゲームオーバー
        if($_SESSION['human']->getHp() <= 0){
          gameOver();
          $gameOverFlg = true;
        }
      }else{

        //ボスを倒した場合はゲームクリア
        if($_SESSION['monster'] instanceof Boss){
          $gameClearFlg = true;

        }else{
        //モンスターのHPが０以下の場合は新しくモンスター生成
        History::set($_SESSION['monster']->getName().'をたおした！');
        $_SESSION['knockDownCount'] = $_SESSION['knockDownCount'] + 1;

        //5分の１の確率で神さま登場
        if(!mt_rand(0,4)){
        createGod();
        }

        createMonster();
       }
      }
    }

    //逃げる
    if($escapeFlg){
      History::set('逃げた！');
      createMonster();
    }

    //回復
    if($healFlg){
      $healPoint = mt_rand(10,100);
      $_SESSION['healCount'] = $_SESSION['healCount'] + 1;
      error_log('ヒールカウント：'.$_SESSION['healCount']);
      $_SESSION['human']->setHp($_SESSION['human']->getHp() + $healPoint);
      History::set('HPが'.$healPoint.'ポイント回復した！');

      //もし最大HPを超える場合は最大HPで返す
      if($_SESSION['human']->getHp() > $_SESSION['human']->getMaxHp() ){
        $_SESSION['human']->setHp($_SESSION['human']->getMaxHp() );
      }
    }


    //神さま出現時
    if(!empty($_SESSION['god'])){

      //回復を選択した場合
      if(!empty($_POST['god-heal'])){
        godHeal();
        unset($_SESSION['god']);
        createMonster();
      }
      //攻撃力UPを選択した場合
      if(!empty($_POST['god-power'])){
        godPower();
        unset($_SESSION['god']);
        createMonster();
      }
      //最大HP上昇の場合
      if(!empty($_POST['god-defense'])){
        godDefense();
        unset($_SESSION['god']);
        createMonster();
      }
    }
  }
  if($titleFlg){
    $_POST = array();
    session_unset();
  }
}


?>

<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="utf-8">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DotGothic16&display=swap" rel="stylesheet">
    <title>オブジェクト指向部　OP</title>
    <style>
      html{
        font-size: 62.5%;
        font-family: 'DotGothic16', sans-serif;
      }
      body{
        margin: 0 auto;
        padding: 12rem 0 2rem 0;
        background: #fbfbfa;
        text-align: center;
        font-size: 1.6rem;
      }
      .title{
        margin: 0;
        padding: 4rem 0 0 0;
        margin-bottom: 2rem;
      }
      .main-area{
        width: 45%;
        margin: 2.5rem auto;
        color: #fff;
        background: #141414;
      }
      input[type='submit']{
        border: none;
        padding: 1.5rem;
        background: none;
        color: #fff;
        cursor:pointer;
        font-family: 'DotGothic16', sans-serif;
      }
      input[type='submit']:hover{
        background: #999696;
      }
      .content{
        display: flex;
        position: relative;
      }
      .command{
        text-align: left;
        width: 30%;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        border: 2px solid #fff;
        margin-left: 9rem;
        margin-bottom: 5rem;
      }
      .player{
        border: 2px solid #fff;
        position: absolute;
        right: 14%;
        padding: 1rem 3rem;
        text-align: left;
      }
      .history{
        margin: 2rem 9rem;
        padding: 3rem 0;
        border: 2px solid #fff;
        box-sizing:border-box;
        max-height:90px;
        overflow-y: auto;
      }
      .monster-img{
        width:95%;
        box-sizing:border-box;
        margin:2rem auto;
        max-height:250px;
      }
      form{
        padding-bottom: 3rem;
      }

    </style>
  </head>
  <body>
    <h2>DAREDAYO QUEST</h2>
    <div class="site-width" style="position: relative;">
      <div class="main-area">

        <?php if($gameOverFlg){?>
          <!--ゲームオーバー画面-->
          <h3  class="title" style="color:#ff244b;">GAME OVER...</h3>
          <form method="post">
            <input type="submit" name="start-hero" value="　勇者　でリトライ">
            <input type="submit" name="start-wizard" value="魔法使いでリトライ">
          </form>

        <?php }elseif($gameClearFlg){?>
          <!--ゲームクリア画面-->
          <h3  class="title" style="color:#f7c554;">GAME CLEAR!!</h3>
          <img src="img/img3.png">
          <form method="post">
            <input type="submit" name="start-hero" value="　勇者　でスタート">
            <input type="submit" name="start-wizard" value="魔法使いでスタート">
          </form>

        <!--スタート画面-->
        <?php }elseif(empty($_SESSION)){?>
          <h3 class="title">GAME START?</h3>
          <form method="post">
            <input type="submit" name="start-hero" value="　勇者　でスタート">
            <input type="submit" name="start-wizard" value="魔法使いでスタート">
          </form>

        <?php }else{ ?>

        <!--ゲーム画面-->
        <!--神様登場時の選択肢ver-->
        <?php if($godFlg){ ?>
        <div class="show">
          <p style="padding:2rem 0;"><?php echo $_SESSION['god']->getGodName(); ?>があらわれた！</p>
          <div class="show-about" style="width:40%; margin:0 auto;">
            <img src="<?php echo $_SESSION['god']->getGodImg(); ?>" class="monster-img">
            <p style="text-align:left;">倒したモンスターの数 ： <?php echo $_SESSION['knockDownCount']; ?></p>
          </div>
          <p class="history"><?php echo mb_substr($_SESSION['history'],0,60).'...'; ?></p>
        </div>
        <div class="content">
        <!--コマンド-->
        <div class="command">
          <form method="post">
            <input type="submit" name="god-heal"       value="▶︎回復してもらう　">
            <input type="submit" name="god-power"      value="▶︎強くしてもらう　">
            <input type="submit" name="god-defense"    value="▶︎丈夫にしてもらう">
            <input type="submit" name="title"          value="▶︎タイトルへ">
          </form>
        </div>
        <!--プレイヤー情報-->
        <div class="player" >
          <p style="margin:0; margin-bottom:3rem;"><?php echo $_SESSION['human']->getName(); ?></p>
          <p>HP : <?php echo $_SESSION['human']->getHp(); ?></p>
          <p>MP : <?php if(!empty($_SESSION['human']->getMp()) ){ echo $_SESSION['human']->setMp($_SESSION['human']->getMp()); }else{ echo 0;} ?></p>
        </div>
      </div>


      <?php }else{ ?>
        <!--通常時ver-->

        <div class="show">
          <p style="padding:2rem 0;"><?php echo $_SESSION['monster']->getName(); ?>があらわれた！</p>
          <div class="show-about" style="width:40%; margin:0 auto;">
            <img src="<?php echo $_SESSION['monster']->getImg(); ?>" class="monster-img">
            <p style="text-align:left;">モンスターのHP :
              <?php if(!empty($_SESSION['boss_flg'])){
                error_log('ボスの分岐に入ってればこっち');
                echo $_SESSION['monster']->getHp();
              }else{
                error_log('モンスターの方');
                echo $_SESSION['monster']->getHp(); }?>
           </p>
            <p style="text-align:left;">倒したモンスターの数 ： <?php echo $_SESSION['knockDownCount']; ?></p>
          </div>
          <p class="history"><?php echo mb_substr($_SESSION['history'],0,60).'...'; ?></p>
        </div>

        <div class="content">
          <!--コマンド-->
          <div class="command">
            <form method="post" style="padding-bottom:1rem;">
              <!--通常ver-->
              <input type="submit" name="escape" value="▶︎逃げる　">
              <input type="submit" name="attack" value="▶︎攻撃する">
              <input type="submit" name="heal"  value="▶︎回復する" style="<?php if($_SESSION['healCount'] >= 3) echo 'display:none;'; ?>"><!--３回までしか使えない-->
              <input type="submit" name="title" value="▶︎タイトルへ">
            </form>
          </div>
          <!--プレイヤー情報-->
          <div class="player" >
            <p style="margin:0; margin-bottom:3rem;"><?php echo $_SESSION['human']->getName(); ?></p>
            <p>HP : <?php echo $_SESSION['human']->getHp(); ?></p>
            <p>MP : <?php if(!empty($_SESSION['human']->getMp()) ){ echo $_SESSION['human']->getMp(); }else{ echo 0 ;} ?></p>
          </div>
        </div>
      <?php } ?>
    <?php } ?>
    </div>
  </div>

  </body>
</html>
