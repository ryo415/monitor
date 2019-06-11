package main

import (
	//"bufio"
	"encoding/json"
	"fmt"
	"github.com/franela/goreq"
	"io/ioutil"
	"os"
	"path/filepath"
	"time"
)

type config struct {
	Server struct {
		URL string `json:"url"`
	} `json:"server"`
	Mail struct {
		Address  string `json:"address"`
		Password string `json:"password"`
		To       string `json:"to"`
		Port     int    `json:"port"`
		SMTP     string `json:"smtp"`
	} `json:"mail"`
}

func main() {
	d := time.Now()
	monitor_file_path, _ := os.Executable()
	monitor_dir_path := filepath.Dir(monitor_file_path)
	config_file_path := monitor_dir_path + "/config.json"
	time_format := "2000-00-00 00:00:00"

	jsonfile, err := ioutil.ReadFile(config_file_path)
	if err != nil {
		panic("config.jsonを作成してください")
	}

	var json_data config
	if err := json.Unmarshal(jsonfile, &json_data); err != nil {
		panic(err)
	}

	//	data_dir := "/opt/monitor/data"
	//	var write_str string

	// 対象サーバにリクエスト投げる
	res, err := goreq.Request{
		Method:  "GET",
		Uri:     json_data.Server.URL,
		Timeout: 10000 * time.Millisecond,
	}.Do()
	// 返答が返ってきた場合
	if err == nil {
		//		response := res.StatusCode
		//		write_str =
		//		fmt.Println(response)
		//		fmt.Println(d.Format(time_format))
		fmt.Println("OK")
	} else {
		fmt.Println("NG")
	}
}
