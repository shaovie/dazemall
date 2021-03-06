-- tinyint     0~127
-- smallint    0~32767
-- int         0~2187483647
-- bigint
-- engine=InnoDB  or  engine=MyISAM

-- md5 len = 32

-- 表名前缀说明
-- u: 用户相关的表
-- o: 订单相关的表
-- g: 商品相关的表
-- s: 系统全局的表
-- b: 运营后台的表

-- ---------------------------------用户相关-----------------------------------
-- 用户基本信息表
drop table if exists u_user;
create table u_user (
    id                  int unsigned not null auto_increment,

    phone               char(11) not null default '',
    passwd              char(32) not null default '',

    nickname            varchar(255) not null default '',
    sex                 tinyint not null default 0,                 # 性别 1:男 2:女 0:未知
    headimgurl          varchar(255) not null default '',           # 用户头像

    cash_amount         decimal(10,2) not null default 0.0,         # 现金余额

    state               tinyint not null default 0,                 # 用户状态

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    index idx_phone(`phone`),
    index idx_nickname(`nickname`)
)engine=InnoDB default charset=utf8;

-- 用户详情表
drop table if exists u_user_detail;
create table u_user_detail (
    id                  int unsigned not null auto_increment,

    user_id             int unsigned not null default 0,
    jifen               int unsigned not null default 0,            # 积分数量

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_user_id(`user_id`)
)engine=InnoDB default charset=utf8;

-- 微信用户信息表
drop table if exists u_wx_user;
create table u_wx_user (
    id                  int unsigned not null auto_increment,

    user_id             int unsigned not null default 0,
    openid              varchar(63) not null default '',
    nickname            varchar(255) not null default '',
    sex                 tinyint not null default 0,                 # 性别 1:男 2:女 0:未知
    headimgurl          varchar(255) not null default '',           # 用户头像
    province            varchar(60) not null default '',            # 省
    city                varchar(60) not null default '',            # 市
    subscribe           tinyint not null default 0,                 # 是否关注 0/1
    subscribe_time      int not null default 0,                     # 关注时间(取最后一次关注)
    subscribe_from      tinyint not null default 0,                 # 关注方式(仅记首次) 1:已经关注
                                                                    # 2:普通关注 3:场景二维码
    unionid             varchar(63) not null default '',            # 腾讯平台唯一ID
    lng                 decimal(12,8) not null default '0.0',       # 经度180.12345678
    lat                 decimal(12,8) not null default '0.0',       # 纬度180.12345678

    state               tinyint not null default 0,                 # 用户状态

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间
    atime               int not null default 0,                     # 与公众号交互时间

    primary key (`id`),
    unique key key_user_id(`user_id`),
    unique key key_openid(`openid`)
)engine=InnoDB default charset=utf8;

-- 用户地址表
drop table if exists u_address;
create table u_address (
    id                  int unsigned not null auto_increment,

    user_id             int unsigned not null default 0,

    re_name             varchar(31) not null default '',            # 收件人姓名
    re_phone            char(11) not null default '',               # 收件人手机号
    addr_type           tinyint not null default 0,                 # 地址类型 0:未知 1:公司 2:家庭

    province_id         int not null default 0,                     # 省
    city_id             int not null default 0,                     # 市
    district_id         int not null default 0,                     # 区
    detail_addr         varchar(255) not null default '',           # 详细街道地址
    re_id_card          varchar(18) not null default '',            # 收件人身份证

    is_default          tinyint not null default 0,                 # 是否为默认地址 0/1

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    index idx_user_id(`user_id`)
)engine=InnoDB default charset=utf8;

-- 购物车表
drop table if exists u_cart;
create table u_cart (
    id                  int unsigned not null auto_increment,

    user_id             int unsigned not null default 0,

    goods_id            int unsigned not null default 0,            # 商品ID
    sku_attr            varchar(36) not null default '',            # sku属性
    sku_value           varchar(60) not null default '',            # sku属性值
    amount              int unsigned not null default 0,            # 商品数量

    attach              varchar(255) not null default '',           # 购物车商品附属信息(json)

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    unique key user_id_goods_info(`user_id`, `goods_id`, `sku_attr`, `sku_value`)
)engine=InnoDB default charset=utf8;

-- 用户优惠券表
drop table if exists u_coupon;
create table u_coupon (
    id                  int unsigned not null auto_increment,

    user_id             int unsigned not null default 0,

    coupon_id           int unsigned not null default 0,            # 优惠券ID
    use_time            int not null default 0,                     # 优惠券使用时间
    state               tinyint not null default 0,                 # 状态 0:未使用 1:已使用

    -- 冗余信息
    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    name                varchar(255) not null default '',           # 优惠券名称
    remark              varchar(255) not null default '',           # 优惠券备注
    coupon_amount       decimal(10,2) not null default 0.0,         # 优惠券面额
    order_amount        decimal(10,2) not null default 0.0,         # 订单限定金额
    category_id         int unsigned not null default 0,            # 限定品类

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_user_id_coupon_id(`user_id`, `coupon_id`)
)engine=InnoDB default charset=utf8;

-- 用户账户流水
drop table if exists u_bill;
create table u_bill (
    id                  int unsigned not null auto_increment,

    user_id             int unsigned not null default 0,
    order_id            varchar(20) not null default '',            # 01 + 150223 + 492933 + 32
    order_pay_id        varchar(20) not null default '',            # 01 + 150223 + 492933 + 32
    bill_type           tinyint not null default 0,                 # 流水类型 1:收入 2:支出
    bill_from           int not null default 0,                     # 流水来源
    amount              decimal(10,2) not null default 0.0,         # 本次交易金额
    left_amount         decimal(10,2) not null default 0.0,         # 之后结余
    remark              varchar(255) not null default '',           # 备注

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_user_id_bill_type(`user_id`, `bill_type`)
)engine=InnoDB default charset=utf8;

-- ---------------------------------订单相关-----------------------------------
-- 订单基础表
drop table if exists o_order;
create table o_order (
    id                  int unsigned not null auto_increment,

    order_id            varchar(20) not null default '',            # 01 + 150223 + 492933 + 32
    order_pay_id        varchar(20) not null default '',            # 01 + 150223 + 492933 + 32
    user_id             int unsigned not null default 0,

    -- 收货信息
    re_name             varchar(31) not null default '',            # 收件人姓名
    re_phone            char(11) not null default '',               # 收件人手机号
    addr_type           tinyint not null default 0,                 # 地址类型 0:未知 1:公司 2:家庭
    province_id         int not null default 0,                     # 省
    city_id             int not null default 0,                     # 市
    district_id         int not null default 0,                     # 区
    detail_addr         varchar(255) not null default '',           # 详细街道地址
    re_id_card          varchar(18) not null default '',            # 收件人身份证

    -- 状态
    pay_state           tinyint not null default 0,                 # 0:未支付 1c支付成功
    pay_time            int not null default 0,                     # 支付时间
    order_state         tinyint not null default 0,                 # 0:创建 1:完成 2:取消
    delivery_state      tinyint not null default 0,                 # 0:未发货 1:发货中 2:已签收 3: 确认收货
    deliveryman_id      int unsigned not null default 0,            # 快递员ID
    delivery_time       int not null default 0,                     # 发货时间

    order_amount        decimal(10,2) not null default 0.0,         # 订单总金额
    ol_pay_amount       decimal(10,2) not null default 0.0,         # 在线支付金额
    ac_pay_amount       decimal(10,2) not null default 0.0,         # 账户余额支付金额
    ol_pay_type         tinyint not null default 0,                 # 在线支付方式 0:非在线支付
                                                                    # 1:支付宝 2:微信 3:银联
    coupon_pay_amount   decimal(10,2) not null default 0.0,         # 优惠券支付金额     
    coupon_id           int unsigned not null default 0,            # 用户优惠券ID
    postage             decimal(10,2) not null default 0.0,         # 邮费

    order_env           tinyint not null default 0,                 # 下单环境：1:IOS 2:Android 3:WX
    remark              varchar(255) not null default '',           # 客户备注信息
    sys_remark          varchar(255) not null default '',           # 系统备注信息

    attach              varchar(255) not null default '',           # json格式附属信息


    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间
    m_user              varchar(31) not null default '',            # 修改人

    primary key (`id`),
    unique key order_id(`order_id`),
    unique key order_pay_id(`order_pay_id`),
    index idx_user_id_order_state(`user_id`, `order_state`),
    index idx_user_id_pay_state(`user_id`, `pay_state`),
    index idx_re_phone(`re_phone`),
    index idx_ctime(`ctime`)
)engine=InnoDB default charset=utf8;

-- 订单商品表
drop table if exists o_order_goods;
create table o_order_goods (
    id                  int unsigned not null auto_increment,

    order_id            varchar(20) not null default '',            # 01 + 150223 + 492933 + 32

    -- 商品快照
    goods_id            int unsigned not null default 0,            # 商品ID
    sku_attr            varchar(600) not null default '',           # sku属性
    sku_value           varchar(600) not null default '',           # sku属性值
    amount              int unsigned not null default 0,            # 商品数量
    price               decimal(10,2) not null default 0.0,         # 商品价格
    bar_code            varchar(63) not null default '',            # 条形码

    state               tinyint not null default 0,                 # 0:待发货   1:已出库   2:已发货 3:已收货
                                                                    # 4:申请退货 5:退货成功 6:退货失败
                                                                    # 7:申请换货 8:换货成功 9:换货失败
    commented           tinyint not null default 0,                 # 是否评论过 

    attach              varchar(255) not null default '',           # 订单商品附属信息(json)

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间
    m_user              varchar(31) not null default '',            # 修改人

    primary key (`id`),
    index idx_order_id(`order_id`)
)engine=InnoDB default charset=utf8;

-- ---------------------------------商品相关表-----------------------------------
-- 商品表
drop table if exists g_goods;
create table g_goods (
    id                  int unsigned not null auto_increment,

    name                varchar(127) not null default '',           # 商品名
    category_id         int unsigned not null default 0,            # 商品类别ID
    market_price        decimal(10,2) not null default 0.0,         # 商品市场价(仅用作展示)
    sale_price          decimal(10,2) not null default 0.0,         # 商品销售价        
    jifen               int unsigned not null default 0,            # 商品购买赠送积分
    sort                int not null default 0,                     # 排序
    tag                 varchar(255) not null default '',           # 右上角标签
    state               tinyint not null default 0,                 # 商品状态
                                                                    # 0:无效 1:有效
                                                                    # 2:上架-展示在商城中

    image_url           varchar(255) not null default '',           # 展示主图
    like_count          int unsigned not null default 0,            # 点赞计数

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    index idx_category_id_sort(`category_id`, `sort`),
    index idx_name(`name`)
)engine=InnoDB default charset=utf8;

-- 商品详情表
drop table if exists g_goods_detail;
create table g_goods_detail (
    id                  int unsigned not null auto_increment,

    goods_id            int unsigned not null default 0,            # 商品ID
    detail              text not null,                              # 商品详细描述
    image_urls          varchar(2048) not null default '',          # 商品轮播图片(json格式)
                                                                    # {"1":{"sort":1,"url":"xx"}}

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_goods_id(`goods_id`)
)engine=InnoDB default charset=utf8;

-- 商品分类表
drop table if exists g_category;
create table g_category (
    id                  int unsigned not null auto_increment,

    category_id         int unsigned not null default 0,            # 品类ID
    sort                int not null default 0,                     # 顺序
    name                varchar(255) not null default '',           # 品类名
    state               tinyint not null default 0,                 # sku状态 0:有效 1:无效

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_category_id(`category_id`)
)engine=InnoDB default charset=utf8;

-- sku属性
drop table if exists g_sku_attr;
create table g_sku_attr (
    id                  int unsigned not null auto_increment,

    attr                varchar(90) not null default '',            # sku属性
    state               tinyint not null default 0,                 # sku状态 0:有效 1:无效

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间
    m_user              varchar(31) not null default '',            # 修改人

    primary key (`id`),
    unique key key_attr(`attr`)
)engine=InnoDB default charset=utf8;
insert into g_sku_attr(attr,state,ctime,mtime,m_user)values('默认',1,unix_timestamp(),unix_timestamp(),'admin');

-- sku值
drop table if exists g_sku_value;
create table g_sku_value (
    id                  int unsigned not null auto_increment,

    attr_id             int unsigned not null default 0,            # 对应属性ID
    value               varchar(90) not null default '',            # sku属性值
    state               tinyint not null default 0,                 # sku状态 0:有效 1:无效

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间
    m_user              varchar(31) not null default '',            # 修改人

    primary key (`id`),
    unique key key_attr_id_value(`attr_id`, `value`)
)engine=InnoDB default charset=utf8;
insert into g_sku_value(attr_id,value,state,ctime,mtime,m_user)values(1,'默认',1,unix_timestamp(),unix_timestamp(),'admin');

-- 商品sku表(价格是sku的一个属性)
drop table if exists g_goods_sku;
create table g_goods_sku (
    id                  int unsigned not null auto_increment,

    goods_id            int unsigned not null default 0,            # 商品ID
    sku_attr            varchar(36) not null default '',            # sku属性
    sku_value           varchar(90) not null default '',            # sku属性值
    state               tinyint not null default 0,                 # sku状态 0:有效 1:无效

    sale_price          decimal(10,2) not null default 0.0,         # 销售价

    amount              int not null default 0,                     # 库存数量
    sold_amount         int not null default 0,                     # 销售总量

    bar_code            varchar(63) not null default '',            # 条形码

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间
    m_user              varchar(31) not null default '',            # 修改人

    primary key (`id`),
    unique key key_goods_id_sku(`goods_id`, `sku_attr`, `sku_value`)
)engine=InnoDB default charset=utf8;

-- 商品点赞表
drop table if exists g_goods_like;
create table g_goods_like (
    id                  int unsigned not null auto_increment,

    goods_id            int unsigned not null default 0,            # 商品ID
    user_id             int unsigned not null default 0,            # 评论用户ID

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    unique key key_goods_id_user_id(`goods_id`, `user_id`)
)engine=InnoDB default charset=utf8;

-- 商品评价表
drop table if exists g_goods_comment;
create table g_goods_comment (
    id                  int unsigned not null auto_increment,

    goods_id            int unsigned not null default 0,            # 商品ID
    order_id            varchar(20) not null default '',            # 01 + 150223 + 492933 + 32

    user_id             int unsigned not null default 0,            # 评论用户ID
    nickname            varchar(255) not null default '',           # 评论用户名(冗余数据)
    score               int unsigned not null default 0,            # 商品评分
    content             varchar(1024) not null default '',          # 商品评价
    image_urls          varchar(2048) not null default '',          # 商品评价图片(json格式)
                                                                    # {"url":["http://xx",""]}

    kf_reply            varchar(1024) not null default '',          # 客服回复
    like_count          int unsigned not null default 0,            # 点赞计数
    state               tinyint not null default 0,                 # 评论状态 0:无效 1:有效

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_goods_id_state(`goods_id`, `state`),
    index idx_user_id_goods_id_order_id(`user_id`, `goods_id`, `order_id`)
)engine=InnoDB default charset=utf8;

-- 商品评价点赞表
drop table if exists g_goods_comment_like;
create table g_goods_comment_like (
    id                  int unsigned not null auto_increment,

    comment_id          int unsigned not null default 0,            # 评价ID
    user_id             int unsigned not null default 0,            # 评论用户ID

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_comment_id_user_id(`comment_id`, `user_id`)
)engine=InnoDB default charset=utf8;

-- --------------------------------营销工具类表-----------------------------------
-- 营销活动信息表
drop table if exists m_activity;
create table m_activity (
    id                  int unsigned not null auto_increment,

    title               varchar(255) not null default '',           # 展示标题
    show_area           int not null default 0,                     # 显示区域
    image_url           varchar(255) not null default '',           # 展示图片
    image_urls          varchar(2048) not null default '',          # 商品轮播图片(json格式)
    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    sort                int not null default 0,                     # 顺序
    wx_share_title      varchar(255) not null default '',           #
    wx_share_desc       varchar(255) not null default '',           #
    wx_share_img        varchar(255) not null default '',           #

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_time(`begin_time`, `end_time`)
)engine=InnoDB default charset=utf8;

-- 活动商品列表
drop table if exists m_activity_goods;
create table m_activity_goods (
    id                  int unsigned not null auto_increment,

    act_id              int unsigned not null default 0,            # 活动ID
    goods_id            int unsigned not null default 0,            # 商品ID
    sort                int not null default 0,                     # 顺序

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    unique key key_act_id_goods_id(`act_id`, `goods_id`)
)engine=InnoDB default charset=utf8;

-- 定时调价
drop table if exists m_timing_mprice;
create table m_timing_mprice (
    id                  int unsigned not null auto_increment,

    goods_sku_id        int unsigned not null default 0,            # 商品SKU ID
    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    limit_num           int not null default 0,                     # 限购数量
    to_price            decimal(10,2) not null default 0.0,         # 调整价格 
    resume_price        decimal(10,2) not null default 0.0,         # 到期后恢复价
    synch_sale_price    tinyint not null default 0,                 # 同步商品展示销售价
    state               tinyint not null default 0,                 # 状态 0:未调整 1:调整成功 2:恢复

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_goods_sku_id(`goods_sku_id`),
    index idx_begin_time(`begin_time`)
)engine=InnoDB default charset=utf8;

-- 定时上下架
drop table if exists m_timing_updown;
create table m_timing_updown (
    id                  int unsigned not null auto_increment,

    goods_id            int unsigned not null default 0,            # 商品ID
    begin_time          datetime not null default '0000-00-00 00:00:00', # 开始时间
    end_time            datetime not null default '0000-00-00 00:00:00', # 结束时间
    timing_type         int not null default 0,                     # 定时类型 1:一次 2:每天
    opt_type            int not null default 0,                     # 操作类型 1:上架 2:下架(无效)
    resume_state        tinyint not null default 0,                 # 到期后恢上下架状态
    state               tinyint not null default 0,                 # 状态 0:未调整 1:调整成功 2:恢复

    ctime               int not null default 0,                     # 创建时间
    mtime               int not null default 0,                     # 修改时间

    primary key (`id`),
    unique key key_goods_id(`goods_id`),
    index idx_begin_time(`begin_time`)
)engine=InnoDB default charset=utf8;

-- 优惠券配置表
drop table if exists m_coupon_cfg;
create table m_coupon_cfg (
    id                  int unsigned not null auto_increment,

    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    name                varchar(255) not null default '',           # 优惠券名称
    remark              varchar(255) not null default '',           # 优惠券备注
    coupon_amount       decimal(10,2) not null default 0.0,         # 优惠券面额
    order_amount        decimal(10,2) not null default 0.0,         # 订单限定金额
    category_id         int unsigned not null default 0,            # 限定品类

    state               tinyint not null default 0,                 # 状态 0:无效 1:有效

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`)
)engine=InnoDB default charset=utf8;

-- 优惠券发放配置表
drop table if exists m_coupon_give_cfg;
create table m_coupon_give_cfg (
    user_reg_coupon     varchar(255) not null default '',           # 新人注册赠送优惠券
    order_full_coupon   varchar(255) not null default '',           # 单笔订单满赠送优惠券
    order_amount        decimal(10,2) not null default 0.0,         # 订单限定金额

    mtime               int not null default 0                      # 修改时间

)engine=InnoDB default charset=utf8;
insert into m_coupon_give_cfg values();

-- banner配置
drop table if exists m_banner;
create table m_banner (
    id                  int unsigned not null auto_increment,

    show_area           int not null default 0,                     # 显示区域
    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    image_url           varchar(255) not null default '',           # 展示图片
    link_type           int not null default 0,                     # 链接类型
    link_value          varchar(255) not null default '',           # 链接值
    remark              varchar(255) not null default '',           # 备注
    sort                int not null default 0,                     # 顺序

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_time_sort(`begin_time`, `end_time`, `sort`)
)engine=InnoDB default charset=utf8;

-- 页面内普通商品模块配置
drop table if exists m_goods_module;
create table m_goods_module (
    id                  int unsigned not null auto_increment,

    title               varchar(255) not null default '',           # 展示标题
    image_url           varchar(255) not null default '',           # 展示主图
    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    sort                int not null default 0,                     # 顺序

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_time_sort(`begin_time`, `end_time`, `sort`)
)engine=InnoDB default charset=utf8;

-- 页面内普通商品模块商品列表配置
drop table if exists m_goods_module_glist;
create table m_goods_module_glist (
    id                  int unsigned not null auto_increment,

    module_id           int unsigned not null default 0,            # 模块ID
    goods_id            int unsigned not null default 0,            # 普通商品ID
    sort                int not null default 0,                     # 顺序

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_module_id_sort(`module_id`, `sort`),
    index idx_module_id_goods_id(`module_id`, `goods_id`)
)engine=InnoDB default charset=utf8;

-- 秒杀活动
drop table if exists m_miao_sha_goods;
create table m_miao_sha_goods (
    id                  int unsigned not null auto_increment,

    goods_id            int unsigned not null default 0,            # 商品ID
    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    price               decimal(10,2) not null default 0.0,         # 秒杀价格
    sort                int not null default 0,                     # 顺序

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_time_sort(`begin_time`, `end_time`, `sort`)
)engine=InnoDB default charset=utf8;

-- --------------------------------全局配置表---------------------------------
drop table if exists s_global_config;
create table s_global_config (
    free_postage        decimal(10,2) not null default 28.0,        # 免邮费，订单金额
    postage             decimal(10,2) not null default 3.0,         # 邮费
    kucun_alarm         int unsigned not null default 20,           #
    kucun_alarm_users   varchar(255) not null default '',           #
    kucun_alarm_tpl     varchar(255) not null default '',           #
    search_key          varchar(255) not null default '',           #
    wx_share_title      varchar(255) not null default '',           #
    wx_share_desc       varchar(255) not null default '',           #
    wx_share_img        varchar(255) not null default '',           #

    mtime               int not null default 0,                     # 创建时间
    m_user              varchar(31) not null default ''             # 修改人

)engine=InnoDB default charset=utf8;
insert into s_global_config values();

-- --------------------------------报表相关-----------------------------------
drop table if exists r_order_per_day;
create table r_order_per_day (
    id                  int unsigned not null auto_increment,

    order_num           int unsigned not null default 0,            #
    seller_amount       decimal(10,2) not null default 0.0,         #

    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_time(`begin_time`, `end_time`)
)engine=InnoDB default charset=utf8;

drop table if exists r_goods_per_day;
create table r_goods_per_day (
    id                  int unsigned not null auto_increment,

    goods_id            int unsigned not null default 0,            # 商品ID
    sku_attr            varchar(36) not null default '',            # sku属性
    sku_value           varchar(60) not null default '',            # sku属性值
    seller_num          int unsigned not null default 0,            #
    seller_amount       decimal(10,2) not null default 0.0,         #

    begin_time          int not null default 0,                     # 开始时间
    end_time            int not null default 0,                     # 结束时间
    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_time(`begin_time`, `end_time`)
)engine=InnoDB default charset=utf8;

-- --------------------------------后台相关-----------------------------------
drop table if exists b_employee;
create table b_employee(
    id                  int unsigned not null auto_increment,

    account             varchar(32) not null default '',
    passwd              char(32) not null default '',

    name                varchar(255) not null default '',
    phone               char(11) not null default '',
    state               tinyint not null default 0,                 # 用户状态

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`),
    index idx_account(`account`),
    index idx_phone(`phone`)
)engine=InnoDB default charset=utf8;
insert into b_employee(account,passwd,name,phone,state,ctime)values('admin',md5('dazeadmin'),'管理员','13800138000',1,unix_timestamp());

drop table if exists b_deliveryman;
create table b_deliveryman(
    id                  int unsigned not null auto_increment,

    name                varchar(255) not null default '',
    phone               char(11) not null default '',
    state               tinyint not null default 0,                 # 用户状态

    ctime               int not null default 0,                     # 创建时间

    primary key (`id`)
)engine=InnoDB default charset=utf8;
