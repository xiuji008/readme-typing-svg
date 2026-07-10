<!-- markdownlint-disable MD033 MD041 -->
<p align="center">
  <h3 align="center">⌨️ Readme Typing SVG</h3>
</p>

<p align="center">
  <img src="http://192.168.31.195:2800?lines=Type+messages+everywhere!;Add+a+bio+to+your+profile!;Add+a+description+to+your+repo!;Make+your+readme+stand+out!&font=Fira%20Code&center=true&width=380&height=50&duration=4000&pause=1000" alt="Example Usage - README Typing SVG">
</p>

<p align="center">
  <a href="https://github.com/search?q=extension%3Amd+%22https+readme+typing+svg%22&type=Code" alt="Users" title="Repo users">
    <img src="https://freshidea.com/jonah/app/github-search-results/readme-typing-svg/index.php"/></a>
  <a href="https://discord.gg/fPrdqh3Zfu" alt="Discord" title="Dev Pro Tips Discussion & Support Server">
    <img src="https://img.shields.io/discord/819650821314052106?color=7289DA&logo=discord&logoColor=white&style=for-the-badge"/></a>
</p>
<!-- markdownlint-enable MD033 -->

## ⚡ Quick setup

1. Copy-paste the markdown below into your GitHub profile README
2. Replace the value after `?lines=` with your text. Separate lines of text with semicolons and use `+` or `%20` for spaces.
3. Adjust the width parameter (see below) to fit the full width of your text.

```md
![Typing SVG](http://192.168.31.195:2800?font=Fira+Code&pause=1000&width=435&lines=The+five+boxing+wizards+jump+quickly)
```

4. Star the repo 😄

## ⚙ Demo site

Here you can easily customize your Typing SVG with a live preview.

<http://192.168.31.195:2800/demo/>

[![Demo Site](https://user-images.githubusercontent.com/20955511/183703055-42ec8754-d84c-414f-8132-a02974224aa1.gif "Demo Site")](https://readme-typing-svg.demolab.com/demo/)

## 🛠 改造说明

本仓库在官方 [readme-typing-svg](https://github.com/DenverCoder1/readme-typing-svg) 基础上做了以下本地化改造：

- **中文 / CJK 字体支持**：内置字体列表新增 41 个中文及中日韩字体（如 Noto Sans/Serif SC、Ma Shan Zheng、ZCOOL 系列、LXGW WenKai TC、Noto TC/HK、Klee One、Zen 系列、Noto Sans JP 等），总字体数达 85 个。
- **字体源可配置（国内镜像）**：默认使用国内镜像 `https://fonts.googleapis.cn` 抓取 Google 字体，支持通过 `font_source` 参数覆盖，源不可达时自动回退官方 `fonts.googleapis.com`。
- **字体统一目录**：字体配置文件 `fonts.json` 与上传的字体文件统一存放在 `src/fonts/`，便于 Docker 部署时挂载持久化，避免重建镜像后丢失。
- **字体速查表**：Demo 页新增「字体速查表」，点击任意字体即可复制其名称并填入“字体”框。
- **可视化文字编辑器**：可点击单词或拖选词语，弹出工具栏设置颜色、加粗、字号、字体，底层写入与后端一致的 `[[...]]` 语法。
- **上传字体**：支持上传 `.ttf` / `.zip` 字体文件，自动登记到 `fonts.json` 的本地字体列表，支持中文名称。
- **下载 SVG**：预览区新增「下载 SVG」按钮，导出的 SVG 已内嵌 `@font-face` base64 字体，自包含、可直接用于 GitHub README，无需依赖外部字体服务。
- **复制代码去描述**：复制到剪贴板的 Markdown / HTML 示例代码已去掉图片描述（分别为 `![]()` 与无 `alt` 的 `<img>`）。

> 说明：SVG 预览与导出均通过 `curl` 抓取字体并内联，确保渲染自包含；若服务器无法访问外部字体源，将自动回退并使用系统/默认字体。

## 🚀 Example usage

Below are links to profiles where you can see Readme Typing SVGs in action!

[![Jonah Lawrence](https://github.com/DenverCoder1.png?size=60)](https://github.com/DenverCoder1 "Jonah Lawrence on GitHub")
[![Jini by Rentalz.com](https://i.imgur.com/TtuoKCs.png)](https://jini.rentalz.com/ "Jini by Rentalz.com")
[![Waren Gonzaga](https://github.com/warengonzaga.png?size=60)](https://github.com/warengonzaga "Waren Gonzaga on GitHub")
[![8BitJonny](https://github.com/8BitJonny.png?size=60)](https://github.com/8BitJonny "8BitJonny on GitHub")
[![Aditya Raute](https://github.com/adityaraute.png?size=60)](https://github.com/adityaraute "Aditya Raute on GitHub")
[![Shiva Sankeerth Reddy](https://github.com/ShivaSankeerth.png?size=60)](https://github.com/ShivaSankeerth "Shiva Sankeerth Reddy on GitHub")
[![Tarun Kamboj](https://github.com/Tarun-Kamboj.png?size=60)](https://github.com/Tarun-Kamboj "Tarun Kamboj on GitHub")
[![T.A.Vignesh](https://github.com/tavignesh.png?size=60)](https://github.com/tavignesh "T.A.Vignesh on GitHub")
[![William J. Ghelfi](https://github.com/trumbitta.png?size=60)](https://github.com/trumbitta "William J. Ghelfi on GitHub")
[![Mano Bharathi M](https://i.imgur.com/Audc6L9.png)](https://github.com/ManoBharathi93 "Mano Bharathi M on GitHub")
[![Shivam Yadav](https://github.com/sudoshivam.png?size=60)](https://github.com/sudoshivam "Shivam Yadav on GitHub")
[![Pratik Pingale](https://github.com/PROxZIMA.png?size=60)](https://github.com/PROxZIMA "Pratik Pingale on GitHub")
[![Vydr'Oz](https://github.com/VydrOz.png?size=60)](https://github.com/VydrOz "Vydr'Oz on GitHub")
[![Caroline Heloíse](https://github.com/Carol42.png?size=60)](https://github.com/Carol42 "Caroline Heloíse on GitHub")
[![PriyanshK09](https://github.com/PriyanshK09.png?size=60)](https://github.com/PriyanshK09 "PriyanshK09 on GitHub")
[![Thakur Ballary](https://github.com/thakurballary.png?size=60)](https://github.com/thakurballary "Thakur Ballary on GitHub")
[![NiceSapien](https://github.com/nicesapien.png?size=60)](https://github.com/nicesapien "NiceSapien on GitHub")
[![Manthan Ank](https://github.com/manthanank.png?size=60)](https://github.com/manthanank "Manthan Ank on GitHub")
[![Ronny Coste](https://github.com/lertsoft.png?size=60)](https://github.com/lertsoft "Ronny Coste on GitHub")
[![Vishal Beep](https://github.com/vishal-beep136.png?size=60)](https://github.com/Vishal-beep136 "Vishal Beep on GitHub")
[![wiz64](https://github.com/wiz64.png?size=60)](https://github.com/wiz64 "wiz64 on GitHub")
[![Aquarian Blake](https://github.com/Aquarius-blake.png?size=60)](https://github.com/Aquarius-blake "Aquarian Blake on GitHub")
[![D3vil0p3r](https://github.com/D3vil0p3r.png?size=60)](https://github.com/D3vil0p3r "D3vil0p3r on GitHub")
[![EliusHHimel](https://github.com/EliusHHimel.png?size=60)](https://github.com/EliusHHimel "EliusHHimel on GitHub")
[![jcs090218](https://github.com/jcs090218.png?size=60)](https://github.com/jcs090218 "jcs090218 on GitHub")
[![Rishabh2804](https://github.com/Rishabh2804.png?size=60)](https://github.com/Rishabh2804 "Rishabh2804 on GitHub")
[![shalinibhatt](https://github.com/shalinibhatt.png?size=60)](https://github.com/shalinibhatt "shalinibhatt on GitHub")
[![UlisesAlexanderAM](https://github.com/UlisesAlexanderAM.png?size=60)](https://github.com/UlisesAlexanderAM "UlisesAlexanderAM on GitHub")
[![SpookyJelly](https://github.com/SpookyJelly.png?size=60)](https://github.com/SpookyJelly "SpookyJelly on GitHub")
[![majidtdeni666](https://github.com/majidtdeni666.png?size=60)](https://github.com/majidtdeni666 "majidtdeni666 on GitHub")
[![GalexY727](https://github.com/galexy727.png?size=60)](https://github.com/galexy727 "GalexY727 on GitHub")
[![HectorSaldes](https://github.com/HectorSaldes.png?size=60)](https://github.com/HectorSaldes "HectorSaldes on GitHub")
[![Ash-codes18](https://github.com/Ash-codes18.png?size=60)](https://github.com/Ash-codes18 "Ash-codes18 on GitHub")
[![Maagnitude](https://github.com/Maagnitude.png?size=60)](https://github.com/Maagnitude "Maagnitude on GitHub")
[![cracker911181](https://github.com/cracker911181.png?size=60)](https://github.com/cracker911181 "cracker911181 on GitHub")
[![quiet-node](https://github.com/quiet-node.png?size=60)](https://github.com/quiet-node "quiet-node on GitHub")
[![kaustubh43](https://github.com/kaustubh43.png?size=60)](https://github.com/kaustubh43 "kaustubh43 on GitHub")
[![kaisunoo](https://github.com/kaisunoo.png?size=60)](https://github.com/kaisunoo "kaisunoo on GitHub")
[![meyer-pidiache](https://github.com/meyer-pidiache.png?size=60)](https://github.com/meyer-pidiache "Meyer Pidiache on GitHub")
[![jeremiahseun](https://github.com/jeremiahseun.png?size=60)](https://github.com/jeremiahseun "Jeremiah Erinola on GitHub")
[![Anand Purushottam](https://github.com/creativepurus.png?size=60)](https://github.com/creativepurus "Anand Purushottam 🇮🇳 on GitHub ☕")
[![Greg Chism](https://github.com/Gchism94.png?size=60)](https://github.com/Gchism94 "Greg Chism 🤘 on GitHub")
[![turbomaster95](https://github.com/turbomaster95.png?size=60)](https://github.com/turbomaster95 "turbomaster95 🗿 🇮🇳 on GitHub ☕")
[![K1rsN7](https://github.com/K1rsN7.png?size=60)](https://github.com/K1rsN7 "K1rsN7 on GitHub💪")
[![codesbyahsen](https://github.com/codesbyahsen.png?size=60)](https://github.com/codesbyahsen "AHSEN ALEE on GitHub")
[![Freddywhest](https://github.com/Freddywhest.png?size=60)](https://github.com/Freddywhest "Alfred Nti on GitHub")
[![Shiro-cha](https://github.com/Shiro-cha.png?size=60)](https://github.com/Shiro-cha "Shiro Yukami on Github")
[![Abid-Nafi](https://github.com/MohammedAbidNafi.png?size=60)](https://github.com/MohammedAbidNafi "Abid Nafi on Github")
[![Srijan-Baniyal](https://github.com/Srijan-Baniyal.png?size=60)](https://github.com/Srijan-Baniyal "Srijan Baniyal on Github")
[![BrunoOliveiraS](https://github.com/BrunoOliveiraS.png?size=60)](https://github.com/BrunoOliveiraS "Bruno Oliveira on Github")
[![zidk](https://github.com/zidk.png?size=60)](https://github.com/zidk "Pablo Gonzalez on Github")
[![tshr-d-dragon](https://github.com/tshr-d-dragon.png?size=60)](https://github.com/tshr-d-dragon "Tushar Patil on Github")
[![DeveshYadav13](https://github.com/DeveshYadav13.png?size=60)](https://github.com/DeveshYadav13 "Devesh Yadav on Github")
[![HauseMasterZ](https://github.com/HauseMasterZ.png?size=60)](https://github.com/HauseMasterZ "HauseMaster on Github")
[![hyskoniho](https://github.com/hyskoniho.png?size=60)](https://github.com/hyskoniho "hyskoniho on Github")
[![elvisisvan](https://github.com/elvisisvan.png?size=60)](https://github.com/elvisisvan "elvisisvan on Github")
[![Nquenan](https://github.com/Nquenan.png?size=60)](https://github.com/Nquenan "Nquenan on Github")
[![akhilnev](https://github.com/akhilnev.png?size=60)](https://github.com/akhilnev "Akhilesh Nevatia on Github")
[![mannysoft](https://github.com/mannysoft.png?size=60)](https://github.com/mannysoft "Manny Isles on Github")
[![LinThitHtwe](https://github.com/LinThitHtwe.png?size=60)](https://github.com/LinThitHtwe "LinThitHtwe on Github")
[![Elio-Aliaj](https://github.com/Elio-Aliaj.png?size=60)](https://github.com/Elio-Aliaj "Elio-Aliaj on Github")
[![presentformyfriends](https://github.com/presentformyfriends.png?size=60)](https://github.com/presentformyfriends "presentformyfriends on Github")
[![Ad7amstein](https://github.com/Ad7amstein.png?size=60)](https://github.com/Ad7amstein "Ad7amstein on Github")
[![LakshmanKishore](https://github.com/LakshmanKishore.png?size=60)](https://github.com/LakshmanKishore "LakshmanKishore on Github")
[![mateusadada](https://github.com/mateusadada.png?size=60)](https://github.com/mateusadada "mateusadada on Github")
[![fasakinhenry](https://github.com/fasakinhenry.png?size=60)](https://github.com/fasakinhenry "fasakinhenry on Github")
[![YousifAbozid](https://github.com/YousifAbozid.png?size=60)](https://github.com/YousifAbozid "YousifAbozid on Github")
[![hheinsoee](https://github.com/hheinsoee.png?size=60)](https://github.com/hheinsoee "hheinsoee on Github")
[![lucmsilva651](https://github.com/lucmsilva651.png?size=60)](https://github.com/lucmsilva651 "lucmsilva651 on Github")
[![ashertenenbaum](https://github.com/ashertenenbaum.png?size=60)](https://github.com/ashertenenbaum "ashertenenbaum on Github")
[![0dxplt](https://github.com/0dxplt.png?size=60)](https://github.com/0dxplt "0dxplt on Github")
[![HerobrineTV](https://github.com/HerobrineTV.png?size=60)](https://github.com/HerobrineTV "HerobrineTV on Github")
[![Borketh](https://github.com/Borketh.png?size=60)](https://github.com/Borketh "Borketh on Github")
[![Jafeth Yahuma](https://github.com/Callmeproteus.png?size=60)](https://github.com/Callmeproteus "Callmeproteus on GitHub")
[![João Pedro](https://github.com/JotaP07.png?size=60)](https://github.com/JotaP07 "JP on GitHub")
[![suzukimain](https://github.com/suzukimain.png?size=60)](https://github.com/suzukimain "suzukimain on Github")
[![caesar013](https://github.com/caesar013.png?size=60)](https://github.com/caesar013 "caesar013 on Github")
[![amir78729](https://github.com/amir78729.png?size=60)](https://github.com/amir78729 "Amir on Github")
[![AJsuper007](https://github.com/AJsuper007.png?size=60)](https://github.com/AJsuper007 "AJsuper007 on Github")
[![ABAN26](https://github.com/ABAN26.png?size=60)](https://github.com/ABAN26 "ABAN26 on Github")
[![Soham More](https://github.com/SohamMore100.png?size=60)](https://github.com/SohamMore100 "Soham More on GitHub")
[![Yogi Hariyani](https://github.com/yobro7292.png?size=60)](https://github.com/Yobro7292 "Yogi Hariyani on GitHub")
[![Antônio Nascimento](https://github.com/Ninja1375.png?size=60)](https://github.com/Ninja1375 "Antônio Nascimento on GitHub")
[![Ishaan Rastogi](https://github.com/TridentifyIshaan.png?size=60)](https://github.com/TridentifyIshaan "Tridentify Ishaan on GitHub")
[![Eligijus Ciza](https://github.com/krimmyy.png?size=60)](https://github.com/krimmyy "Eligijus Ciza on GitHub")
[![Ashish Vaghela](https://github.com/Ashish-CodeJourney.png?size=60)](https://github.com/Ashish-CodeJourney "Ashish Vaghela on GitHub")
[![Snoopy1866](https://github.com/Snoopy1866.png?size=60)](https://github.com/Snoopy1866 "Snoopy1866 on GitHub")
[![Sarthak Krishak](https://github.com/SarthakKrishak.png?size=60)](https://github.com/SarthakKrishak "Sarthak Krishak on GitHub")
[![Austin Musuya](https://github.com/AustinMusuya.png?size=60)](https://github.com/AustinMusuya "Austin Musuya on GitHub")
[![Rohit](https://github.com/EngineerRohit01.png?size=60)](https://github.com/EngineerRohit01 "Rohit on GitHub")
[![Sandeep Prasad](https://github.com/Sandeep-Petwal.png?size=60)](https://github.com/sandeep-Petwal "Sandeep Prasad on GitHub")
[![Saad Hussain](https://github.com/saadhusayn.png?size=60)](https://github.com/saadhusayn "Saad Hussain on Github")
[![Rahul Raj](https://github.com/Theglassofdata.png?size=60)](https://github.com/Theglassofdata "Rahul Raj")
[![Aditya Singh](https://github.com/EchoSingh.png?size=60)](https://github.com/EchoSingh "Aditya Singh on Github")
[![Muhammad Noraeii](https://github.com/Muhammad-Noraeii.png?size=60)](https://github.com/Muhammad-Noraeii "Muhammad Noraeii on Github")
[![Harry Skerritt](https://github.com/user-attachments/assets/392d404f-b0af-4fab-b4f7-120a36ffc3f4)](https://github.com/Harry-Skerritt "Harry-Skerritt on Github")
[![Madhurima Rawat](https://github.com/madhurimarawat.png?size=60)](https://github.com/madhurimarawat "Madhurima Rawat on Github")
[![wfxey](https://github.com/wfxey.png?size=60)](https://github.com/wfxey "wfxey on Github")
[![Lixiao Zhu](https://github.com/zhulixiao.png?size=60)](https://github.com/zhulixiao "Lixiao Zhu on Github")
[![Ahmed Nassar](https://github.com/AhmedNassar7.png?size=60)](https://github.com/AhmedNassar7 "Ahmed Nassar on Github")

Feel free to [open a PR](https://github.com/xiuji008/readme-typing-svg/issues/21#issue-870549556) and add yours!

## 🔧 Options

|    Parameter    |                                   Details                                   |  Type   |                                                      Example                                                      |
| :-------------: | :-------------------------------------------------------------------------: | :-----: | :---------------------------------------------------------------------------------------------------------------: |
|     `lines`     |       Text to display with lines separated by `;` and `+` for spaces        | string  |                                        `First+line;Second+line;Third+line`                                        |
|    `height`     |             Height of the output SVG in pixels (default: `50`)              | integer |                                                Any positive number                                                |
|     `width`     |             Width of the output SVG in pixels (default: `400`)              | integer |                                                Any positive number                                                |
|     `size`      |                     Font size in pixels (default: `20`)                     | integer |                                                Any positive number                                                |
|     `font`      |                     Font family (default: `monospace`)                      | string  |                                            Any font from Google Fonts                                             |
|     `color`     |                    Color of the text (default: `36BCF7`)                    | string  |                                         Hex code without # (eg. `F724A9`)                                         |
|  `background`   |             Background color of the text (default: `00000000`)              | string  |                                         Hex code without # (eg. `FEFF4C`)                                         |
|    `center`     |    `true` to center text or `false` for left aligned (default: `false`)     | boolean |                                                 `true` or `false`                                                 |
|    `vCenter`    |  `true` to center vertically or `false`(default) to align above the center  | boolean |                                                 `true` or `false`                                                 |
|   `multiline`   |  `true` to wrap lines or `false` to retype on one line (default: `false`)   | boolean |                                                 `true` or `false`                                                 |
|   `duration`    | Duration of the printing of a single line in milliseconds (default: `5000`) | integer |                                                Any positive number                                                |
|     `pause`     |     Duration of the pause between lines in milliseconds (default: `0`)      | integer |                                              Any non-negative number                                              |
|    `repeat`     |  `true` to loop around to the first line after the last (default: `true`)   | boolean |                                                 `true` or `false`                                                 |
|   `separator`   |     Separator used between lines in the lines parameter (default: `;`)      | string  |                                               `;`, `;;`, `/`, etc.                                                |
| `letterSpacing` |                     Letter spacing (default: `normal`)                      | string  | Any css values for the [letter-spacing](https://developer.mozilla.org/en-US/docs/Web/CSS/letter-spacing) property |

## 📤 Deploying it on your own

If you can, it is preferable to host the files on your own server.

Doing this can lead to better uptime and more control over customization (you can modify the code for your usage).

You can deploy the PHP files on any website server with PHP installed or as a Heroku app.

### Step-by-step instructions for deploying to Heroku

1. Sign in to **Heroku** or create a new account at <https://heroku.com>
2. Click the "Deploy to Heroku" button below

[![Deploy](https://www.herokucdn.com/deploy/button.svg "Deploy to Heroku")](https://heroku.com/deploy?template=https://github.com/xiuji008/readme-typing-svg/tree/main)

3. On the page that comes up, click **"Deploy App"** at the end of the form
4. Once the app is deployed, click **"Manage App"** to go to the dashboard
5. Scroll down to the **Domains** section in the settings to find the URL you will use in place of `readme-typing-svg.demolab.com`

## 🤗 Contributing

Contributions are welcome! Feel free to open an issue or submit a pull request if you have a way to improve this project.

Make sure your request is meaningful and you have tested the app locally before submitting a pull request.

Refer to [CONTRIBUTING.md](/CONTRIBUTING.md) for more details on contributing, installing requirements, and running the application.

## 🙋‍♂️ Support

💙 If you like this project, give it a ⭐ and share it with friends!

<!-- markdownlint-disable MD033 -->
<p align="left">
  <a href="https://www.youtube.com/channel/UCipSxT7a3rn81vGLw9lqRkg?sub_confirmation=1"><img alt="Youtube" title="Youtube" src="https://img.shields.io/badge/-Subscribe-red?style=for-the-badge&logo=youtube&logoColor=white"/></a>
  <a href="https://github.com/sponsors/DenverCoder1"><img alt="Sponsor with Github" title="Sponsor with Github" src="https://img.shields.io/badge/-Sponsor-ea4aaa?style=for-the-badge&logo=github&logoColor=white"/></a>
</p>
<!-- markdownlint-enable MD033 -->

[☕ Buy me a coffee](https://ko-fi.com/jlawrence)

---

Made with ❤️ and PHP

<!-- markdownlint-disable MD033 -->

<a href="https://heroku.com/"><img alt="Powered by Heroku" title="Powered by Heroku" src="https://img.shields.io/badge/-Powered%20by%20Heroku-6567a5?style=for-the-badge&logo=heroku&logoColor=white"/></a>

<!-- markdownlint-enable MD033 -->

This project uses [Twemoji](https://github.com/twitter/twemoji), published under the [CC-BY 4.0 License](https://creativecommons.org/licenses/by/4.0/)
