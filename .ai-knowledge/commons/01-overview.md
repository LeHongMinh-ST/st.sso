
# Tổng quan nghiệp vụ dự án

Đây là một **Hệ thống Quản lý Định danh và Truy cập tập trung (Single Sign-On - SSO)**, được thiết kế riêng cho một **tổ chức giáo dục (như trường đại học)**.

## Mục tiêu chính

Cung cấp một điểm xác thực (đăng nhập) duy nhất cho toàn bộ người dùng (sinh viên, giảng viên, nhân viên...) để truy cập vào các ứng dụng và dịch vụ khác nhau của trường (ví dụ: hệ thống đào tạo, trang web khoa, portal sinh viên...).

## Các nghiệp vụ cốt lõi

### 1. Quản lý người dùng và phân quyền (RBAC - Role-Based Access Control)

**Mô tả:** Hệ thống quản lý tập trung tài khoản và phân quyền cho toàn bộ người dùng trong tổ chức.

**Các chức năng chính:**
- **Quản lý tài khoản người dùng:**
  - Tạo, cập nhật, xóa tài khoản người dùng
  - Quản lý thông tin cá nhân (họ tên, email, mã số sinh viên/giảng viên)
  - Quản lý trạng thái tài khoản (active, inactive, suspended)
  - Đặt lại mật khẩu và yêu cầu đổi mật khẩu lần đầu đăng nhập
  - Liên kết tài khoản với Microsoft Azure (Office 365)

- **Quản lý vai trò (Roles):**
  - Định nghĩa các vai trò trong hệ thống (Super Admin, Admin, Giảng viên, Sinh viên, Nhân viên...)
  - Mỗi vai trò có một tập hợp quyền hạn cụ thể
  - Người dùng có thể có nhiều vai trò cùng lúc

- **Quản lý quyền hạn (Permissions):**
  - Phân quyền chi tiết theo từng chức năng (CRUD operations)
  - Nhóm quyền theo các module (User Management, Faculty Management, Client Management...)
  - Gán quyền cho vai trò và kiểm tra quyền khi người dùng thực hiện hành động

**Luồng nghiệp vụ:**
1. Quản trị viên tạo vai trò mới và gán các quyền tương ứng
2. Gán vai trò cho người dùng (có thể gán nhiều vai trò)
3. Hệ thống kiểm tra quyền khi người dùng truy cập các chức năng
4. Người dùng chỉ có thể thực hiện các hành động mà họ có quyền

### 2. Xác thực tập trung (SSO - Single Sign-On)

**Mô tả:** Cho phép người dùng đăng nhập một lần và truy cập vào tất cả các ứng dụng được tích hợp mà không cần đăng nhập lại.

**Các phương thức xác thực:**
- **Xác thực bằng tài khoản-mật khẩu:**
  - Người dùng đăng nhập bằng username/email và mật khẩu
  - Hỗ trợ tính năng "Nhớ đăng nhập" (Remember Me)
  - Yêu cầu đổi mật khẩu lần đầu đăng nhập nếu là mật khẩu mặc định
  - Kiểm tra và ngăn chặn đăng nhập nếu tài khoản chỉ được phép đăng nhập qua Microsoft

- **Xác thực qua Microsoft Azure (OAuth 2.0):**
  - Tích hợp với Microsoft Azure AD / Office 365
  - Người dùng có thể chọn đăng nhập bằng tài khoản Microsoft
  - Tự động đồng bộ thông tin từ Microsoft về hệ thống
  - Hỗ trợ liên kết tài khoản Microsoft với tài khoản hiện có

**Cơ chế SSO:**
- Sau khi đăng nhập thành công, hệ thống tạo và cấp token (JWT hoặc session token)
- Token chứa thông tin người dùng và quyền hạn
- Các ứng dụng client có thể sử dụng token này để xác thực người dùng
- Token có thời gian hết hạn và có thể được làm mới (refresh)

**Luồng nghiệp vụ đăng nhập:**
1. Người dùng truy cập trang đăng nhập
2. Chọn phương thức đăng nhập (tài khoản-mật khẩu hoặc Microsoft)
3. Xác thực thông tin đăng nhập
4. Kiểm tra quyền và trạng thái tài khoản
5. Tạo session/token và chuyển hướng đến dashboard
6. Người dùng có thể truy cập các ứng dụng khác mà không cần đăng nhập lại

### 3. Quản lý ứng dụng (Clients)

**Mô tả:** Quản trị viên có thể đăng ký và quản lý các ứng dụng được phép sử dụng hệ thống SSO để xác thực người dùng.

**Các chức năng chính:**
- **Đăng ký ứng dụng mới:**
  - Tạo client ID và client secret cho ứng dụng
  - Cấu hình redirect URI (URL callback sau khi xác thực)
  - Thiết lập các quyền (scopes) mà ứng dụng có thể yêu cầu
  - Cấu hình thời gian hết hạn của token

- **Quản lý ứng dụng:**
  - Xem danh sách tất cả các ứng dụng đã đăng ký
  - Cập nhật thông tin ứng dụng (tên, mô tả, redirect URI...)
  - Vô hiệu hóa/kích hoạt ứng dụng
  - Xóa ứng dụng không còn sử dụng
  - Xem lịch sử hoạt động của ứng dụng

- **Bảo mật:**
  - Mỗi ứng dụng có client secret riêng để xác thực
  - Hỗ trợ OAuth 2.0 Authorization Code Flow và Client Credentials Flow
  - Kiểm tra và validate redirect URI để tránh tấn công
  - Rate limiting để bảo vệ API

**Luồng nghiệp vụ tích hợp ứng dụng:**
1. Quản trị viên tạo ứng dụng mới trong hệ thống SSO
2. Nhận client ID và client secret
3. Cấu hình ứng dụng với thông tin nhận được
4. Ứng dụng redirect người dùng đến trang đăng nhập SSO
5. Sau khi đăng nhập thành công, hệ thống redirect về ứng dụng kèm authorization code
6. Ứng dụng đổi authorization code lấy access token
7. Ứng dụng sử dụng access token để gọi API và lấy thông tin người dùng

### 4. Quản lý cơ cấu tổ chức

**Mô tả:** Quản lý cấu trúc tổ chức của trường học, bao gồm các Khoa (Faculty) và Phòng ban (Department).

**Các chức năng chính:**
- **Quản lý Khoa (Faculty):**
  - Tạo, cập nhật, xóa thông tin khoa
  - Quản lý mã khoa, tên khoa, mô tả
  - Xem danh sách người dùng thuộc khoa (sinh viên, giảng viên)
  - Xem danh sách các phòng ban thuộc khoa

- **Quản lý Phòng ban (Department):**
  - Tạo, cập nhật, xóa phòng ban
  - Gán phòng ban vào khoa tương ứng
  - Quản lý mã phòng ban, tên phòng ban
  - Xem danh sách người dùng thuộc phòng ban

- **Liên kết với người dùng:**
  - Gán người dùng vào khoa và phòng ban tương ứng
  - Hỗ trợ tìm kiếm và lọc người dùng theo khoa/phòng ban
  - Xuất danh sách người dùng theo khoa/phòng ban

**Luồng nghiệp vụ:**
1. Quản trị viên tạo cấu trúc tổ chức (Khoa → Phòng ban)
2. Gán người dùng vào khoa và phòng ban tương ứng
3. Hệ thống tự động cập nhật thông tin khi có thay đổi
4. Các ứng dụng khác có thể truy vấn cấu trúc tổ chức qua API

### 5. Nhập liệu hàng loạt (Bulk Import)

**Mô tả:** Cho phép quản trị viên nhập dữ liệu người dùng (chủ yếu là sinh viên) hàng loạt từ file Excel để tiết kiệm thời gian và giảm sai sót.

**Các chức năng chính:**
- **Import từ file Excel:**
  - Hỗ trợ định dạng file Excel (.xlsx, .xls)
  - Validate dữ liệu trước khi import (kiểm tra định dạng, bắt buộc các trường...)
  - Xử lý import theo batch để tránh quá tải hệ thống
  - Hiển thị tiến trình import (progress bar)

- **Xử lý dữ liệu:**
  - Tạo tài khoản mới cho các sinh viên chưa có trong hệ thống
  - Cập nhật thông tin cho các sinh viên đã tồn tại
  - Tự động gán vai trò "Sinh viên" cho các tài khoản mới
  - Tự động tạo mật khẩu mặc định và yêu cầu đổi mật khẩu lần đầu đăng nhập

- **Báo cáo và thông báo:**
  - Hiển thị số lượng thành công/thất bại sau khi import
  - Xuất file log chi tiết các lỗi (nếu có)
  - Gửi thông báo email cho quản trị viên khi import hoàn tất
  - Lưu lịch sử import để theo dõi

**Luồng nghiệp vụ:**
1. Quản trị viên tải file Excel mẫu và điền thông tin sinh viên
2. Upload file Excel lên hệ thống
3. Hệ thống validate dữ liệu và hiển thị preview
4. Xác nhận và bắt đầu import
5. Hệ thống xử lý import theo batch (background job)
6. Hiển thị tiến trình và kết quả import
7. Gửi thông báo khi hoàn tất

**Định dạng file Excel mẫu:**
- Mã số sinh viên (Student Code)
- Họ và tên (First Name, Last Name)
- Email
- Khoa (Faculty)
- Phòng ban (Department)
- Các thông tin khác (nếu có)

### 6. Cung cấp API cho tích hợp

**Mô tả:** Cung cấp các API RESTful an toàn để các ứng dụng bên ngoài có thể tích hợp và lấy thông tin từ hệ thống SSO.

**Các API chính:**
- **Authentication API:**
  - `/api/auth/login` - Đăng nhập và nhận token
  - `/api/auth/logout` - Đăng xuất
  - `/api/auth/refresh` - Làm mới token
  - `/api/auth/user` - Lấy thông tin người dùng hiện tại

- **User API:**
  - `GET /api/users` - Lấy danh sách người dùng (có phân trang và filter)
  - `GET /api/users/{id}` - Lấy thông tin chi tiết người dùng
  - `POST /api/users` - Tạo người dùng mới
  - `POST /api/users/{id}/reset-password` - Đặt lại mật khẩu

- **Faculty API:**
  - `GET /api/faculties` - Lấy danh sách tất cả khoa
  - `GET /api/faculties/{id}` - Lấy thông tin chi tiết khoa
  - `GET /api/faculties/{id}/users` - Lấy danh sách người dùng thuộc khoa
  - `GET /api/faculties/{id}/teachers` - Lấy danh sách giảng viên thuộc khoa
  - `GET /api/faculties/{id}/departments` - Lấy danh sách phòng ban thuộc khoa

- **Department API:**
  - `GET /api/departments` - Lấy danh sách phòng ban
  - `GET /api/departments/{id}` - Lấy thông tin chi tiết phòng ban

**Bảo mật API:**
- Xác thực bằng Bearer Token (JWT)
- Hỗ trợ Client Credentials Flow cho server-to-server communication
- Rate limiting để bảo vệ API khỏi abuse
- CORS configuration để kiểm soát truy cập từ các domain khác
- API Key middleware cho một số endpoint công khai (nhưng vẫn cần client credentials)

**Luồng tích hợp:**
1. Ứng dụng đăng ký trong hệ thống SSO và nhận client credentials
2. Ứng dụng sử dụng client credentials để lấy access token
3. Ứng dụng gọi các API với access token trong header
4. Hệ thống SSO validate token và trả về dữ liệu
5. Ứng dụng sử dụng dữ liệu để hiển thị hoặc xử lý nghiệp vụ

## Tóm tắt

Đây là một hệ thống lõi, đóng vai trò "trái tim" trong việc quản lý danh tính số của toàn bộ người dùng trong một trường học. Hệ thống giúp:

- **Đơn giản hóa:** Người dùng chỉ cần đăng nhập một lần để truy cập tất cả ứng dụng
- **Bảo mật:** Quản lý tập trung tài khoản và phân quyền, giảm thiểu rủi ro bảo mật
- **Hiệu quả:** Tự động hóa các quy trình quản lý người dùng và tích hợp với các hệ thống khác
- **Mở rộng:** Dễ dàng tích hợp thêm các ứng dụng và dịch vụ mới
- **Tuân thủ:** Đảm bảo tuân thủ các quy định về quản lý thông tin người dùng trong tổ chức giáo dục
