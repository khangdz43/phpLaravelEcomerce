(function () {
    const drawer = document.querySelector("[data-cart-drawer]");
    const overlay = document.querySelector("[data-cart-overlay]");
    const itemsTarget = document.querySelector("[data-cart-items]");
    const countTarget = document.querySelector("[data-cart-count]");
    const totalTarget = document.querySelector("[data-cart-total]");
    const checkoutForm = document.querySelector("[data-checkout-form]");

    let currentCartData = { items: [], count: 0, total: 0 };
    let activeCouponDiscount = 0;

    const formatPrice = (value) =>
        new Intl.NumberFormat("vi-VN").format(value || 0) + " đ";

    const getCsrfToken = () =>
        document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

    function toggle(open) {
        if (!drawer || !overlay) return;
        drawer.classList.toggle("is-open", open);
        overlay.classList.toggle("is-open", open);
        document.body.style.overflow = open ? "hidden" : "";
    }

    function render(data) {
        currentCartData = data || { items: [], count: 0, total: 0 };

        if (countTarget) {
            countTarget.textContent = currentCartData.count;
        }

        if (totalTarget) {
            totalTarget.textContent = formatPrice(currentCartData.total);
        }

        if (itemsTarget) {
            if (currentCartData.items && currentCartData.items.length > 0) {
                itemsTarget.innerHTML = currentCartData.items
                    .map(
                        (item) => `
                        <div class="cart-row" data-cart-item-id="${item.id}">
                            <span class="cart-swatch visual-${item.id % 4}"></span>
                            <div class="cart-info">
                                <h3>${escapeHtml(item.name)}</h3>
                                <p class="cart-unit-price">${formatPrice(item.price)}</p>
                                <div class="cart-qty-controls">
                                    <button type="button" class="cart-qty-btn" data-cart-dec="${item.id}" aria-label="Giảm số lượng">−</button>
                                    <span class="cart-qty-val">${item.quantity}</span>
                                    <button type="button" class="cart-qty-btn" data-cart-inc="${item.id}" aria-label="Tăng số lượng">+</button>
                                </div>
                            </div>
                            <div class="cart-actions">
                                <span class="cart-line-total">${formatPrice(item.price * item.quantity)}</span>
                                <button class="cart-remove" data-remove-cart="${item.id}" type="button">Xóa</button>
                            </div>
                        </div>`
                    )
                    .join("");
            } else {
                itemsTarget.innerHTML = '<p class="cart-empty">Giỏ hàng đang trống.<br>Thêm một món đồ để bắt đầu.</p>';
            }
        }

        // Đồng bộ với trang Checkout nếu đang ở trang checkout
        if (checkoutForm) {
            const itemsInput = checkoutForm.querySelector("[data-checkout-items]");
            const summary = document.querySelector("[data-checkout-summary]");
            const subtotalTarget = document.querySelector("[data-checkout-subtotal]");
            const checkoutTotal = document.querySelector("[data-checkout-total]");

            if (itemsInput) {
                const checkoutItems = currentCartData.items.map((i) => ({
                    product_id: i.id,
                    quantity: i.quantity,
                }));
                itemsInput.value = JSON.stringify(checkoutItems);
            }

            if (subtotalTarget) {
                subtotalTarget.textContent = formatPrice(currentCartData.total);
            }

            if (checkoutTotal) {
                const finalTotal = Math.max(0, currentCartData.total - activeCouponDiscount);
                checkoutTotal.textContent = formatPrice(finalTotal);
            }

            if (summary) {
                summary.innerHTML = currentCartData.items.length
                    ? currentCartData.items
                          .map(
                              (item) => `
                              <div class="summary-row" style="display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid var(--line); font-size: 13px;">
                                  <span>${escapeHtml(item.name)} × ${item.quantity}</span>
                                  <strong>${formatPrice(item.price * item.quantity)}</strong>
                              </div>`
                          )
                          .join("")
                    : '<p class="cart-empty" style="padding: 20px; text-align: center; color: var(--muted);">Giỏ hàng đang trống.</p>';
            }
        }
    }

    function escapeHtml(str) {
        return (str || "")
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // --- CÁC HÀM GỌI API BACKEND ---

    async function fetchCart() {
        try {
            const res = await fetch("/cart", {
                headers: {
                    Accept: "application/json",
                },
            });
            if (res.ok) {
                const data = await res.json();
                render(data);
            }
        } catch (err) {
            console.error("Lỗi khi tải giỏ hàng:", err);
        }
    }

    async function addToCart(productId, quantity = 1) {
        try {
            const res = await fetch("/cart", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
                body: JSON.stringify({
                    product_id: Number(productId),
                    quantity: Number(quantity),
                }),
            });

            if (res.ok) {
                const data = await res.json();
                render(data);
                toggle(true);
            } else {
                const err = await res.json();
                alert(err.message || "Không thể thêm sản phẩm vào giỏ hàng.");
            }
        } catch (err) {
            console.error("Lỗi khi thêm sản phẩm:", err);
            alert("Lỗi kết nối khi thêm vào giỏ hàng.");
        }
    }

    async function updateQuantity(productId, quantity) {
        if (quantity < 1) {
            return removeFromCart(productId);
        }

        try {
            const res = await fetch(`/cart/${productId}`, {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json",
                    Accept: "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
                body: JSON.stringify({
                    quantity: Number(quantity),
                }),
            });

            if (res.ok) {
                const data = await res.json();
                render(data);
            } else {
                const err = await res.json();
                alert(err.message || "Không thể cập nhật số lượng.");
            }
        } catch (err) {
            console.error("Lỗi khi cập nhật giỏ hàng:", err);
        }
    }

    async function removeFromCart(productId) {
        try {
            const res = await fetch(`/cart/${productId}`, {
                method: "DELETE",
                headers: {
                    Accept: "application/json",
                    "X-CSRF-TOKEN": getCsrfToken(),
                },
            });

            if (res.ok) {
                const data = await res.json();
                render(data);
            } else {
                const err = await res.json();
                alert(err.message || "Không thể xóa sản phẩm khỏi giỏ hàng.");
            }
        } catch (err) {
            console.error("Lỗi khi xóa sản phẩm:", err);
        }
    }

    // --- EVENT LISTENERS ---

    document.addEventListener("click", (event) => {
        // 1. Thêm vào giỏ hàng
        const addButton = event.target.closest("[data-add-cart]");
        if (addButton) {
            const container = addButton.closest("[data-product-id]");
            if (container) {
                const productId = Number(container.dataset.productId);
                const qtyInput = container.querySelector("[data-qty-input]");
                const quantity = qtyInput ? Math.max(1, parseInt(qtyInput.value, 10) || 1) : 1;
                addToCart(productId, quantity);
            }
            return;
        }

        // 2. Bộ chọn số lượng ở trang chi tiết sản phẩm
        const detailDec = event.target.closest("[data-qty-dec]");
        if (detailDec) {
            const input = detailDec.parentElement?.querySelector("[data-qty-input]");
            if (input) {
                const current = parseInt(input.value, 10) || 1;
                if (current > 1) {
                    input.value = current - 1;
                }
            }
            return;
        }

        const detailInc = event.target.closest("[data-qty-inc]");
        if (detailInc) {
            const input = detailInc.parentElement?.querySelector("[data-qty-input]");
            if (input) {
                const max = parseInt(input.getAttribute("max"), 10) || 99;
                const current = parseInt(input.value, 10) || 1;
                if (current < max) {
                    input.value = current + 1;
                }
            }
            return;
        }

        // 3. Tăng/giảm số lượng bên trong Drawer Giỏ hàng
        const cartDec = event.target.closest("[data-cart-dec]");
        if (cartDec) {
            const productId = Number(cartDec.dataset.cartDec);
            const item = currentCartData.items.find((i) => i.id === productId);
            if (item) {
                updateQuantity(productId, item.quantity - 1);
            }
            return;
        }

        const cartInc = event.target.closest("[data-cart-inc]");
        if (cartInc) {
            const productId = Number(cartInc.dataset.cartInc);
            const item = currentCartData.items.find((i) => i.id === productId);
            if (item) {
                updateQuantity(productId, item.quantity + 1);
            }
            return;
        }

        // 4. Xóa sản phẩm khỏi giỏ hàng
        const removeButton = event.target.closest("[data-remove-cart]");
        if (removeButton) {
            const productId = Number(removeButton.dataset.removeCart);
            removeFromCart(productId);
            return;
        }

        // 5. Đóng/Mở Giỏ hàng
        if (event.target.closest("[data-cart-open]")) {
            toggle(true);
            return;
        }

        if (event.target.closest("[data-cart-close]") || event.target === overlay) {
            toggle(false);
            return;
        }

        // 6. Nút chuyển sang Checkout từ Drawer
        if (event.target.closest("[data-checkout]")) {
            if (!currentCartData.items || currentCartData.items.length === 0) {
                alert("Giỏ hàng của bạn đang trống.");
                return;
            }
            window.location.href = "/checkout";
            return;
        }
    });

    // --- CHECKOUT LOGIC: COUPON & ADDRESS ---

    // Áp dụng coupon preview
    const applyCouponBtn = document.getElementById("apply-coupon-btn");
    const couponInput = document.getElementById("coupon-code-input");
    const couponFeedback = document.getElementById("coupon-feedback");
    const couponDiscountRow = document.getElementById("coupon-discount-row");
    const couponDiscountTarget = document.querySelector("[data-checkout-discount]");

    if (applyCouponBtn && couponInput) {
        applyCouponBtn.addEventListener("click", async () => {
            const code = couponInput.value.trim().toUpperCase();
            if (!code) {
                if (couponFeedback) {
                    couponFeedback.textContent = "Vui lòng nhập mã ưu đãi.";
                    couponFeedback.style.color = "var(--danger)";
                }
                return;
            }

            applyCouponBtn.disabled = true;
            applyCouponBtn.textContent = "Đang kiểm tra...";

            try {
                const res = await fetch("/checkout/coupon-preview", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        Accept: "application/json",
                        "X-CSRF-TOKEN": getCsrfToken(),
                    },
                    body: JSON.stringify({
                        code: code,
                        subtotal: currentCartData.total,
                    }),
                });

                const result = await res.json();

                if (res.ok && result.valid) {
                    activeCouponDiscount = Number(result.discount_amount) || 0;
                    if (couponFeedback) {
                        couponFeedback.textContent = `Áp dụng thành công! Giảm ${formatPrice(activeCouponDiscount)}.`;
                        couponFeedback.style.color = "var(--success)";
                    }
                    if (couponDiscountRow) {
                        couponDiscountRow.style.display = "flex";
                    }
                    if (couponDiscountTarget) {
                        couponDiscountTarget.textContent = `-${formatPrice(activeCouponDiscount)}`;
                    }
                    render(currentCartData);
                } else {
                    activeCouponDiscount = 0;
                    if (couponFeedback) {
                        couponFeedback.textContent = result.message || "Mã giảm giá không hợp lệ hoặc chưa đủ điều kiện.";
                        couponFeedback.style.color = "var(--danger)";
                    }
                    if (couponDiscountRow) {
                        couponDiscountRow.style.display = "none";
                    }
                    render(currentCartData);
                }
            } catch (err) {
                console.error("Lỗi áp dụng coupon:", err);
                if (couponFeedback) {
                    couponFeedback.textContent = "Không thể kiểm tra mã ưu đãi lúc này.";
                    couponFeedback.style.color = "var(--danger)";
                }
            } finally {
                applyCouponBtn.disabled = false;
                applyCouponBtn.textContent = "Áp dụng";
            }
        });
    }

    // Chọn địa chỉ đã lưu
    const savedAddressSelect = document.getElementById("saved-address-select");
    if (savedAddressSelect) {
        savedAddressSelect.addEventListener("change", () => {
            const selected = savedAddressSelect.options[savedAddressSelect.selectedIndex];
            if (!selected || !selected.value) return;

            const addrInput = document.getElementById("input-shipping-address");
            const phoneInput = document.getElementById("input-customer-phone");
            const nameInput = document.getElementById("input-customer-name");

            if (addrInput) addrInput.value = selected.value;
            if (phoneInput && selected.dataset.phone) phoneInput.value = selected.dataset.phone;
            if (nameInput && selected.dataset.name) nameInput.value = selected.dataset.name;
        });
    }

    // Khởi tạo: Tải dữ liệu giỏ hàng từ API khi vào trang
    fetchCart();
})();
