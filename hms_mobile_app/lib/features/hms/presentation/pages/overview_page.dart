import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import 'package:hms_mobile_app/l10n/app_localizations.dart';
import '../widgets/shared_widgets.dart';

class OverviewPage extends StatelessWidget {
  final Map<String, dynamic> stats;
  final List<dynamic> sales;
  final List<dynamic> expenses;
  final List<dynamic> rooms;
  final List<dynamic> bookings;
  final String selectedDateFilter;
  final DateTimeRange? customDateRange;
  final bool isHmsEnabled;
  
  const OverviewPage({
    Key? key, 
    required this.stats, 
    required this.sales,
    required this.expenses,
    required this.rooms,
    required this.bookings,
    required this.selectedDateFilter,
    required this.customDateRange,
    required this.isHmsEnabled,
  }) : super(key: key);

  String _formatCurrency(dynamic value) {
    if (value == null) return "0.00";
    final parsed = double.tryParse(value.toString());
    if (parsed == null) return value.toString();
    return parsed.toStringAsFixed(2);
  }

  @override
  Widget build(BuildContext context) {
    final l10n = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    
    // Calculate values dynamically based on filtered lists
    final double totalSales = sales.fold(0.0, (sum, sale) => sum + (double.tryParse(sale['final_total']?.toString() ?? '0') ?? 0.0));
    final double totalExpenses = expenses.fold(0.0, (sum, exp) => sum + (double.tryParse(exp['final_total']?.toString() ?? '0') ?? 0.0));
    final double netProfit = totalSales - totalExpenses;

    final grossProfitStr = _formatCurrency(totalSales);
    final netProfitStr = _formatCurrency(netProfit);
    final totalExpensesStr = _formatCurrency(totalExpenses);

    final List<String> chartLabels = [];
    final List<double> chartSalesValues = [];
    final List<double> chartExpenseValues = [];
    _calculateChartData(sales, expenses, selectedDateFilter, customDateRange, chartLabels, chartSalesValues, chartExpenseValues);

    final double maxSales = chartSalesValues.fold(0.0, (max, val) => val > max ? val : max);
    final double maxExpenses = chartExpenseValues.fold(0.0, (max, val) => val > max ? val : max);
    final double maxChartVal = maxSales > maxExpenses ? maxSales : maxExpenses;

    // Calculate Occupancy Ratio
    int bookedRoomsCount = 0;
    final now = DateTime.now();
    for (var room in rooms) {
      bool isBooked = false;
      for (var booking in bookings) {
        final status = booking['status']?.toString().toLowerCase();
        if (status != 'confirmed' && status != 'pending') continue;
        if (booking['check_out'] != null && booking['check_out'].toString().isNotEmpty) continue;

        final lines = booking['hms_booking_lines'] as List<dynamic>? ?? [];
        for (var line in lines) {
          try {
            final arrivalStr = booking['hms_booking_arrival_date_time'];
            final departureStr = booking['hms_booking_departure_date_time'];
            if (arrivalStr == null || departureStr == null) continue;

            final arrival = DateTime.parse(arrivalStr);
            final departure = DateTime.parse(departureStr);

            final roomIdMatch = line['hms_room_id']?.toString() == room['id']?.toString();

            if (roomIdMatch &&
                now.isAfter(arrival.subtract(const Duration(hours: 12))) &&
                now.isBefore(departure.add(const Duration(hours: 12)))) {
              isBooked = true;
              break;
            }
          } catch (_) {}
        }
        if (isBooked) break;
      }
      if (isBooked) bookedRoomsCount++;
    }

    final int totalRoomsCount = rooms.length;
    final double occupancyPercentage = totalRoomsCount == 0 ? 0.0 : (bookedRoomsCount / totalRoomsCount);

    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(vertical: 12.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          // 🚀 REAL-TIME HOTEL OCCUPANCY STATUS GAUGE
          if (isHmsEnabled)
            GlassCard(
              child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      l10n.liveHotelOccupancy,
                      style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: isDark ? Colors.white : Colors.black87),
                    ),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: const Color(0xFF6366F1).withOpacity(0.12),
                        borderRadius: BorderRadius.circular(10),
                      ),
                      child: Text(
                        "${(occupancyPercentage * 100).toStringAsFixed(0)}% ${l10n.booked}",
                        style: const TextStyle(color: Color(0xFF6366F1), fontSize: 11, fontWeight: FontWeight.bold),
                      ),
                    )
                  ],
                ),
                const SizedBox(height: 18),
                ClipRRect(
                  borderRadius: BorderRadius.circular(10),
                  child: LinearProgressIndicator(
                    value: occupancyPercentage,
                    minHeight: 10,
                    backgroundColor: isDark ? const Color(0xFF0F172A) : const Color(0xFFE5E7EB),
                    valueColor: const AlwaysStoppedAnimation<Color>(Color(0xFF6366F1)),
                  ),
                ),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      "$bookedRoomsCount ${l10n.room} / $totalRoomsCount ${l10n.booked}",
                      style: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8), fontWeight: FontWeight.w500),
                    ),
                    Text(
                      "${totalRoomsCount - bookedRoomsCount} ${l10n.available}",
                      style: const TextStyle(fontSize: 12, color: Colors.green, fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
              ],
            ),
          ),

          // 📊 GLOWING SPLINE CURVED LINE CHART
          GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      "Revenue & Expense Analytics",
                      style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: isDark ? Colors.white : Colors.black87),
                    ),
                    Text(
                      "Max: Rs. ${_formatCurrency(maxChartVal)}",
                      style: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                Row(
                  children: [
                    Container(width: 10, height: 10, decoration: const BoxDecoration(color: Color(0xFF6366F1), shape: BoxShape.circle)),
                    const SizedBox(width: 6),
                    const Text("Sales Income", style: TextStyle(fontSize: 11, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                    const SizedBox(width: 16),
                    Container(width: 10, height: 10, decoration: const BoxDecoration(color: Color(0xFFEF4444), shape: BoxShape.circle)),
                    const SizedBox(width: 6),
                    const Text("Expenses", style: TextStyle(fontSize: 11, color: Color(0xFF94A3B8), fontWeight: FontWeight.bold)),
                  ],
                ),
                const SizedBox(height: 18),
                SizedBox(
                  height: 130,
                  width: double.infinity,
                  child: CustomPaint(
                    painter: SalesChartPainter(chartSalesValues, chartExpenseValues, isDark),
                  ),
                ),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: List.generate(chartLabels.length, (index) {
                    final showLabel = chartLabels.length <= 7 || index % 2 == 0 || index == chartLabels.length - 1;
                    return Text(
                      showLabel ? chartLabels[index] : "", 
                      style: const TextStyle(fontSize: 10, fontWeight: FontWeight.bold, color: Color(0xFF94A3B8)),
                    );
                  }),
                )
              ],
            ),
          ),

          // 💰 MAIN FINANCIAL OVERVIEW GRID
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 4.0),
            child: Text(l10n.keyMetrics, style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: isDark ? Colors.white : Colors.black87)),
          ),
          const SizedBox(height: 4),
          
          Row(
            children: [
              Expanded(
                child: _buildGridMetricCard(l10n.income, grossProfitStr, Icons.trending_up, Colors.green),
              ),
              Expanded(
                child: _buildGridMetricCard(l10n.expenses, totalExpensesStr, Icons.money_off, Colors.redAccent),
              ),
            ],
          ),
          _buildFullWidthMetricCard(l10n.netProfit, netProfitStr, Icons.account_balance_wallet, const Color(0xFF6366F1)),
          
          const SizedBox(height: 16),

          // 🗂️ COMPLETE BUSINESS SUMMARY DETAIL
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 4.0),
            child: Text(l10n.detailedOperatingStatements, style: TextStyle(fontSize: 15, fontWeight: FontWeight.bold, color: isDark ? Colors.white : Colors.black87)),
          ),
          const SizedBox(height: 4),
          GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                _buildSummaryRow("Total Sells / Invoices", _formatCurrency(totalSales)),
                _buildSummaryRow("Sell Returns", _formatCurrency(stats['total_sell_return'])),
                _buildSummaryRow("Sell Discount Given", _formatCurrency(stats['total_sell_discount'])),
                const Divider(color: Colors.white12, height: 20),
                _buildSummaryRow("Total Purchases", _formatCurrency(stats['total_purchase'])),
                _buildSummaryRow("Purchase Returns", _formatCurrency(stats['total_purchase_return'])),
                _buildSummaryRow("Purchase Discount Received", _formatCurrency(stats['total_purchase_discount'])),
                const Divider(color: Colors.white12, height: 20),
                _buildSummaryRow("Operating Expenses", _formatCurrency(totalExpenses)),
                _buildSummaryRow("Expense Recoveries", _formatCurrency(stats['total_expense_recover'])),
                _buildSummaryRow("Total Shipping Charges", _formatCurrency(stats['total_shipping_charges'])),
                _buildSummaryRow("Reward Point Redemptions", _formatCurrency(stats['total_reward_amount'])),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildSummaryRow(String label, dynamic value) {
    final displayValue = value != null ? value.toString() : "0.00";
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(fontSize: 13, color: Color(0xFF94A3B8), fontWeight: FontWeight.w500)),
          Text("Rs. $displayValue", style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold)),
        ],
      ),
    );
  }

  Widget _buildGridMetricCard(String title, String value, IconData icon, Color color) {
    return GlassCard(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          CircleAvatar(
            backgroundColor: color.withOpacity(0.12),
            radius: 20,
            child: Icon(icon, color: color, size: 20),
          ),
          const SizedBox(height: 12),
          Text(title, style: const TextStyle(fontSize: 12, color: Color(0xFF94A3B8), fontWeight: FontWeight.w600)),
          const SizedBox(height: 4),
          Text("Rs. $value", style: const TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
        ],
      ),
    );
  }

  Widget _buildFullWidthMetricCard(String title, String value, IconData icon, Color color) {
    return GlassCard(
      padding: const EdgeInsets.symmetric(horizontal: 20.0, vertical: 16.0),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: color.withOpacity(0.12),
            radius: 24,
            child: Icon(icon, color: color, size: 24),
          ),
          const SizedBox(width: 16),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(title, style: const TextStyle(fontSize: 13, color: Color(0xFF94A3B8), fontWeight: FontWeight.w500)),
                const SizedBox(height: 4),
                Text("Rs. $value", style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  void _calculateChartData(
    List<dynamic> sales, 
    List<dynamic> expenses,
    String filter, 
    DateTimeRange? customRange, 
    List<String> labels, 
    List<double> salesValues,
    List<double> expenseValues
  ) {
    final now = DateTime.now();
    DateTime start;
    DateTime end = now;
    int segments = 6;
    
    if (filter == 'Today') {
      start = DateTime(now.year, now.month, now.day);
      end = start.add(const Duration(hours: 24));
      segments = 6;
      final intervalDuration = const Duration(hours: 4);
      for (int i = 0; i < segments; i++) {
        final intervalStart = start.add(intervalDuration * i);
        final intervalEnd = intervalStart.add(intervalDuration);
        
        final label = "${intervalStart.hour.toString().padLeft(2, '0')}:00";
        labels.add(label);
        
        double salesTotal = 0.0;
        for (var sale in sales) {
          try {
            final date = DateTime.parse(sale['transaction_date']);
            if (date.isAfter(intervalStart) && date.isBefore(intervalEnd)) {
              salesTotal += double.tryParse((sale['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        salesValues.add(salesTotal);

        double expTotal = 0.0;
        for (var exp in expenses) {
          try {
            final date = DateTime.parse(exp['transaction_date']);
            if (date.isAfter(intervalStart) && date.isBefore(intervalEnd)) {
              expTotal += double.tryParse((exp['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        expenseValues.add(expTotal);
      }
    } 
    else if (filter == 'Week') {
      segments = 7;
      start = DateTime(now.year, now.month, now.day).subtract(const Duration(days: 6));
      final DateFormat dayFormatter = DateFormat('E');
      for (int i = 0; i < segments; i++) {
        final day = start.add(Duration(days: i));
        labels.add(dayFormatter.format(day));
        
        double salesTotal = 0.0;
        for (var sale in sales) {
          try {
            final date = DateTime.parse(sale['transaction_date']);
            if (date.year == day.year && date.month == day.month && date.day == day.day) {
              salesTotal += double.tryParse((sale['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        salesValues.add(salesTotal);

        double expTotal = 0.0;
        for (var exp in expenses) {
          try {
            final date = DateTime.parse(exp['transaction_date']);
            if (date.year == day.year && date.month == day.month && date.day == day.day) {
              expTotal += double.tryParse((exp['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        expenseValues.add(expTotal);
      }
    } 
    else if (filter == 'Month') {
      segments = 6;
      start = DateTime(now.year, now.month, now.day).subtract(const Duration(days: 29));
      final intervalDays = 5;
      for (int i = 0; i < segments; i++) {
        final intervalStart = start.add(Duration(days: i * intervalDays));
        final intervalEnd = intervalStart.add(Duration(days: intervalDays));
        
        labels.add("${intervalStart.day}/${intervalStart.month}");
        
        double salesTotal = 0.0;
        for (var sale in sales) {
          try {
            final date = DateTime.parse(sale['transaction_date']);
            if ((date.isAfter(intervalStart) || date.isAtSameMomentAs(intervalStart)) && 
                date.isBefore(intervalEnd)) {
              salesTotal += double.tryParse((sale['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        salesValues.add(salesTotal);

        double expTotal = 0.0;
        for (var exp in expenses) {
          try {
            final date = DateTime.parse(exp['transaction_date']);
            if ((date.isAfter(intervalStart) || date.isAtSameMomentAs(intervalStart)) && 
                date.isBefore(intervalEnd)) {
              expTotal += double.tryParse((exp['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        expenseValues.add(expTotal);
      }
    } 
    else if (filter == 'Year') {
      segments = 12;
      start = DateTime(now.year, 1, 1);
      final DateFormat monthFormatter = DateFormat('MMM');
      for (int i = 0; i < segments; i++) {
        final monthStart = DateTime(now.year, i + 1, 1);
        final monthEnd = DateTime(now.year, i + 2, 1).subtract(const Duration(days: 1));
        labels.add(monthFormatter.format(monthStart));
        
        double salesTotal = 0.0;
        for (var sale in sales) {
          try {
            final date = DateTime.parse(sale['transaction_date']);
            if (date.year == now.year && date.month == (i + 1)) {
              salesTotal += double.tryParse((sale['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        salesValues.add(salesTotal);

        double expTotal = 0.0;
        for (var exp in expenses) {
          try {
            final date = DateTime.parse(exp['transaction_date']);
            if (date.year == now.year && date.month == (i + 1)) {
              expTotal += double.tryParse((exp['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        expenseValues.add(expTotal);
      }
    } 
    else if (filter == 'All Time') {
      if (sales.isEmpty && expenses.isEmpty) {
        segments = 6;
        for (int i = 0; i < segments; i++) {
          labels.add("");
          salesValues.add(0.0);
          expenseValues.add(0.0);
        }
      } else {
        DateTime? minDate;
        DateTime? maxDate;
        
        for (var sale in sales) {
          try {
            final date = DateTime.parse(sale['transaction_date']);
            if (minDate == null || date.isBefore(minDate)) minDate = date;
            if (maxDate == null || date.isAfter(maxDate)) maxDate = date;
          } catch (_) {}
        }
        for (var exp in expenses) {
          try {
            final date = DateTime.parse(exp['transaction_date']);
            if (minDate == null || date.isBefore(minDate)) minDate = date;
            if (maxDate == null || date.isAfter(maxDate)) maxDate = date;
          } catch (_) {}
        }
        
        if (minDate == null || maxDate == null) {
           minDate = now.subtract(const Duration(days: 30));
           maxDate = now;
        }
        
        if (maxDate.difference(minDate).inDays < 6) {
          minDate = maxDate.subtract(const Duration(days: 5));
        }
        
        segments = 6;
        final totalMs = maxDate.millisecondsSinceEpoch - minDate.millisecondsSinceEpoch;
        final intervalMs = totalMs / segments;
        
        for (int i = 0; i < segments; i++) {
          final intervalStart = DateTime.fromMillisecondsSinceEpoch((minDate.millisecondsSinceEpoch + i * intervalMs).toInt());
          final intervalEnd = DateTime.fromMillisecondsSinceEpoch((minDate.millisecondsSinceEpoch + (i + 1) * intervalMs).toInt());
          
          labels.add("${intervalStart.day}/${intervalStart.month}/${intervalStart.year.toString().substring(2)}");
          
          double salesTotal = 0.0;
          for (var sale in sales) {
            try {
              final date = DateTime.parse(sale['transaction_date']);
              if ((date.isAfter(intervalStart) || date.isAtSameMomentAs(intervalStart)) && 
                  date.isBefore(intervalEnd)) {
                salesTotal += double.tryParse((sale['final_total'] ?? 0).toString()) ?? 0.0;
              }
            } catch (_) {}
          }
          salesValues.add(salesTotal);

          double expTotal = 0.0;
          for (var exp in expenses) {
            try {
              final date = DateTime.parse(exp['transaction_date']);
              if ((date.isAfter(intervalStart) || date.isAtSameMomentAs(intervalStart)) && 
                  date.isBefore(intervalEnd)) {
                expTotal += double.tryParse((exp['final_total'] ?? 0).toString()) ?? 0.0;
              }
            } catch (_) {}
          }
          expenseValues.add(expTotal);
        }
      }
    }
    else if (filter == 'Custom' && customRange != null) {
      segments = 6;
      start = customRange.start;
      end = customRange.end;
      final totalMs = end.millisecondsSinceEpoch - start.millisecondsSinceEpoch;
      final intervalMs = totalMs / segments;
      
      for (int i = 0; i < segments; i++) {
        final intervalStart = DateTime.fromMillisecondsSinceEpoch((start.millisecondsSinceEpoch + i * intervalMs).toInt());
        final intervalEnd = DateTime.fromMillisecondsSinceEpoch((start.millisecondsSinceEpoch + (i + 1) * intervalMs).toInt());
        
        labels.add("${intervalStart.day}/${intervalStart.month}");
        
        double salesTotal = 0.0;
        for (var sale in sales) {
          try {
            final date = DateTime.parse(sale['transaction_date']);
            if ((date.isAfter(intervalStart) || date.isAtSameMomentAs(intervalStart)) && 
                date.isBefore(intervalEnd)) {
              salesTotal += double.tryParse((sale['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        salesValues.add(salesTotal);

        double expTotal = 0.0;
        for (var exp in expenses) {
          try {
            final date = DateTime.parse(exp['transaction_date']);
            if ((date.isAfter(intervalStart) || date.isAtSameMomentAs(intervalStart)) && 
                date.isBefore(intervalEnd)) {
              expTotal += double.tryParse((exp['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        expenseValues.add(expTotal);
      }
    } 
    else {
      segments = 7;
      start = DateTime(now.year, now.month, now.day).subtract(const Duration(days: 6));
      final DateFormat dayFormatter = DateFormat('E');
      for (int i = 0; i < segments; i++) {
        final day = start.add(Duration(days: i));
        labels.add(dayFormatter.format(day));
        
        double salesTotal = 0.0;
        for (var sale in sales) {
          try {
            final date = DateTime.parse(sale['transaction_date']);
            if (date.year == day.year && date.month == day.month && date.day == day.day) {
              salesTotal += double.tryParse((sale['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        salesValues.add(salesTotal);

        double expTotal = 0.0;
        for (var exp in expenses) {
          try {
            final date = DateTime.parse(exp['transaction_date']);
            if (date.year == day.year && date.month == day.month && date.day == day.day) {
              expTotal += double.tryParse((exp['final_total'] ?? 0).toString()) ?? 0.0;
            }
          } catch (_) {}
        }
        expenseValues.add(expTotal);
      }
    }
  }
}
