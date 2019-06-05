# 監視システム

## 前提ソフトウェア
- docker
 
- docker-compose
 
- git

## 手順
1. リポジトリから取得  
```
cd /opt  
git clone git@gh.iiji.jp:inf-dev-ojt/r-kikuchi_monitor.git  
```
  
2. コンフィグ設定  
- monitor/config.jsonを編集  
url: 監視したいサーバのURL  
  
3. コンテナの作成、実行  
- docker-composeを実行する  
```
cd /opt/monitor  
docker-compose build  
docker-compose up -d  
```
    
## ファイル構造  
monitor/  
|- docker-compose.yml :docker-compose用コンフィグファイル  
|- monitor/ :monitor用コンテナディレクトリ  
　　|- monitor.py :httpレスポンス情報取得プログラム  
　　|- config.json :コンフィグファイル  
|- data/  :サーバからの取得情報格納ディレクトリ  
|- web/ :WEB用コンテナディレクトリ  
　　|- html/  
　　　　|- index.html :トップページ  
　　　　|- get_response.php :httpリクエストの情報値を整え、jsスクリプトをhttp_response.phpに渡すプログラム  
　　　　|- resource.php :サーバのリソース情報出力ページ(予定)  
　　|- php/  
　　　　|- http_response.php :httpレスポンスタイムのグラフ出力ページ  
　　　　|- get_resource.php :サーバのリソースの情報値を整え、jsスクリプトをresource.phpに渡すプログラム(予定)  

