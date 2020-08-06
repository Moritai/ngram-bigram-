<?php

class Bigram
{
   /**
    * 文字列を登録・検索用バイグラムに変換する
    *
    * @param string|null $string
    * @return string|null
    */
   public function convert_to_bigram(string $string = null)
   {
       if (is_null($string))
       {
           return null;
       }
      
    //    print_r("生のstring:\n". $string. "\n\n");
      
       /* str_replaceメソッド
       第一引数：検索する文字列
       第二引数：置換文字列（第一引数で指定した検索にひっかかった文字列をここで指定した文字列に変換）
       第三引数：検索対象文字列
 
       改行（消去対象）
       \r = CR (Carriage Return) → Used as a new line character in Mac OS before X
       \n = LF (Line Feed) → Used as a new line character in Unix/Mac OS X
       \r\n = CR + LF → Used as a new line character in Windows
 
       空白消去
       https://hacknote.jp/archives/44395/
       */
       $string = str_replace(array(" ", "　", "\r", "\n", "\r\n", "\t"), "", $string);
    //    print_r("空白や改行を無くした後:\n".$string."\n\n");
 
       /*strip_tagsメソッド
       引数の文字列からHTMLとPHPタグを取り除く */
       $string = strip_tags($string);
    //    print_r("htmlタグ及びphpタグを取り除いた後:\n".$string."\n\n");

      //東京都など検索ノイズになる語を修正する。  
       $string = $this->removeSearchNoise($string);

      $tokens = '';// バイグラム

      if(mb_strlen($string) === 1){
         //一単語だけの場合はそのままtoken（１文字だけの語句）として転置インデックスに登録、検索に用いる
         $tokens = $string;
      }else{      
         /*preg_split
         //1つ目の引数の正規表現には "//u" と unicode で空文字を設定(日本語を正しく認識できるように u オプションは必須)
         ↓日本語を一文字ずつの配列にする。
         http://var.blog.jp/archives/51037242.html
         */
         $character_list = preg_split("//u", $string, -1, PREG_SPLIT_NO_EMPTY);// 1文字づつ配列に分ける
   
         $glue = '';
         foreach ($character_list as $index => $character)
         {
            if (isset($character_list[$index + 1]))
            {
                  $tokens .= $glue.$character.$character_list[$index + 1];
            }
               $glue = " ";
         }
      }

    //    utf8_encode($bigram);
       return $tokens;
   }

   private function removeSearchNoise($string){
      // https://qiita.com/kazu56/items/2c72d187438de07c2503
         //$stringsのなかに'東京都'が含まれている場合、東京に変換する。
         $string = str_replace("東京都","東京", $string);
         return $string;

   }
 
}
 

$string = "東京都に遊びに行く";
$bigram = new Bigram();
print_r("変換後:\n". $bigram->convert_to_bigram($string)."\n\n");

