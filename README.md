# echbay-anti-spam

Plugin này sẽ nhúng thêm một số input ẩn vào form `comment` của wordpress hoặc `product review` của `woocommerce`. Chức măng này cũng có thể áp dụng cho `login form`, plugin `contact form 7` và `checkout form` của woocommerce (kích hoạt bằng cách chọn trong setting của plugin).

Các input ẩn có nhiệm vụ xác định xem dữ liệu gửi đi là bot hay là người dùng thật. Tên các input ẩn đã được mã hóa để tránh việc bot dễ dàng can thiệp vào các input này theo tên input. Các input cũng được dùng css để ẩn thay vì dùng hidden type để gây khó khăn cho việc dò theo type của input.

- Người dùng thật sẽ không có bất kỳ thao tác dữ liệu nào với các input ẩn này nên dữ liệu đầu vào không có gì thay đổi, vì thế sẽ dễ dàng vượt qua khâu so khớp dữ liệu.
- Nếu là bot sẽ có nhiều trường hợp xảy ra, nhưng cơ bản cũng chỉ xoay quanh 1 trong 2 kiểu là thay đổi hoặc không thay đổi giá trị (value) trong input ẩn. Và trong các input ẩn sẽ có những cái không được thay đổi giá trị, nếu bot thay đổi các giá trị này sẽ khiến cho việc so khớp lúc submit bị đứt đoạn. Đồng thời cũng có những input ẩn mặc định không có giá trị (để trống), sau đó có lệnh dùng `jQuery` để gán giá trị cho nó, nếu các giá trị này không được gán thì cũng không vượt qua được khâu so khớp dữ liệu diễn ra khi submit. Tất cả các dữ liệu được gán vào input ẩn này đều tuân theo công thức nhất định, việc gán sai hoặc không gán cũng gây ra đứt đoạn trong quá trình submit.

Về cơ bản, không có plugin nào đảm bảo chống spam 100% mà chỉ giảm thiểu xuống mức thấp nhất có thể. Ngoài ra plugin này cũng cố gắng đáp ứng yêu cầu tiện dụng, cài plugin xong là dùng. Code sử dụng tham số `SECURE_AUTH_SALT` có sẵn của wordpress nên không cần kết nối với bên thứ 3 để thực hiện thao tác xác thực. Thi thoảng bạn cũng có thể thay đổi tham số `SECURE_AUTH_SALT` và các tham số khác trong đoạn `Authentication Unique Keys and Salts` ở file wp-config.php để thay đổi chuỗi bảo mật.
