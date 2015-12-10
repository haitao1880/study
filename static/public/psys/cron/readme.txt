client_daily.php 每日凌晨00:05执行，功能：获取前一日开启平台用户的(去重复)mac地址【若已经存在此mac地址则不添加】。
data_daily.php 每日凌晨00:15分执行，功能：汇总前一日数据。
data_pre_weekly.php 每日凌晨00:25分执行，功能：汇总上周数据。
data_pre_monthly.php 每日凌晨00:35分执行，功能：汇总上月数据。
data_cur_weekly.php 每日凌晨00:45分执行，功能：汇总本周数据。
data_cur_monthly.php 每日凌晨00:55分执行，功能：汇总本月数据。
backup_daily.php 每月1日凌晨01:05执行，功能：转移上月数据到备份表并删除上月数据。
backup_online.php 每月1日凌晨01:15执行，功能：转移上月数据到备份表并删除上月数据。
