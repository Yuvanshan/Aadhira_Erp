import 'package:flutter/material.dart';

// ══════════════════════════════════════════════════════════════════════════════
// GLASSMORPHIC / PREMIUM TRANSPARENT CARD
// ══════════════════════════════════════════════════════════════════════════════
class GlassCard extends StatelessWidget {
  final Widget child;
  final EdgeInsetsGeometry? padding;
  final List<Color>? gradientColors;
  final double borderRadius;
  const GlassCard({
    Key? key,
    required this.child,
    this.padding,
    this.gradientColors,
    this.borderRadius = 24,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(borderRadius),
        gradient: LinearGradient(
          colors: gradientColors ?? (isDark
              ? [
                  const Color(0xFF131A26).withOpacity(0.85),
                  const Color(0xFF0B1019).withOpacity(0.85),
                ]
              : [
                  Colors.white.withOpacity(0.95),
                  const Color(0xFFF3F4F6).withOpacity(0.95),
                ]),
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
        ),
        border: Border.all(color: (isDark ? Colors.white : Colors.black).withOpacity(0.05)),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(isDark ? 0.25 : 0.08),
            blurRadius: 16,
            offset: const Offset(0, 6),
          ),
        ],
      ),
      child: Padding(
        padding: padding ?? const EdgeInsets.all(20.0),
        child: child,
      ),
    );
  }
}

// ══════════════════════════════════════════════════════════════════════════════
// PULSING SHIMMER SKELETON LOADER WIDGET
// ══════════════════════════════════════════════════════════════════════════════
class PulsingShimmer extends StatefulWidget {
  final double width;
  final double height;
  final double borderRadius;
  const PulsingShimmer({
    Key? key,
    required this.width,
    required this.height,
    this.borderRadius = 8,
  }) : super(key: key);

  @override
  State<PulsingShimmer> createState() => _PulsingShimmerState();
}

class _PulsingShimmerState extends State<PulsingShimmer> with SingleTickerProviderStateMixin {
  late AnimationController _controller;

  @override
  void initState() {
    super.initState();
    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1500),
    )..repeat();
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    
    final baseColor = isDark ? const Color(0xFF1E293B) : const Color(0xFFE2E8F0);
    final highlightColor = isDark ? const Color(0xFF334155) : const Color(0xFFF1F5F9);

    return AnimatedBuilder(
      animation: _controller,
      builder: (context, child) {
        return Container(
          width: widget.width,
          height: widget.height,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(widget.borderRadius),
            gradient: LinearGradient(
              colors: [
                baseColor,
                highlightColor,
                baseColor,
              ],
              stops: const [0.0, 0.5, 1.0],
              begin: Alignment(-2.0 + _controller.value * 4.0, -1.0),
              end: Alignment(-1.0 + _controller.value * 4.0, 1.0),
            ),
          ),
        );
      },
    );
  }
}

class ShimmerPage extends StatelessWidget {
  const ShimmerPage({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return SingleChildScrollView(
      padding: const EdgeInsets.symmetric(vertical: 12.0),
      child: Column(
        children: [
          GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const PulsingShimmer(width: 140, height: 16, borderRadius: 4),
                const SizedBox(height: 16),
                const PulsingShimmer(width: double.infinity, height: 10, borderRadius: 4),
                const SizedBox(height: 12),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: const [
                    PulsingShimmer(width: 120, height: 12, borderRadius: 4),
                    PulsingShimmer(width: 80, height: 12, borderRadius: 4),
                  ],
                ),
              ],
            ),
          ),
          GlassCard(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: const [
                PulsingShimmer(width: 160, height: 16, borderRadius: 4),
                const SizedBox(height: 24),
                PulsingShimmer(width: double.infinity, height: 120, borderRadius: 10),
              ],
            ),
          ),
          Row(
            children: const [
              Expanded(
                child: GlassCard(
                  child: PulsingShimmer(width: 80, height: 50, borderRadius: 8),
                ),
              ),
              Expanded(
                child: GlassCard(
                  child: PulsingShimmer(width: 80, height: 50, borderRadius: 8),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}

// ══════════════════════════════════════════════════════════════════════════════
// SPLINE GLOWING SALES CHART PAINTER
// ══════════════════════════════════════════════════════════════════════════════
class SalesChartPainter extends CustomPainter {
  final List<double> salesValues;
  final List<double> expensesValues;
  final bool isDark;
  
  SalesChartPainter(this.salesValues, this.expensesValues, this.isDark);

  @override
  void paint(Canvas canvas, Size size) {
    if (salesValues.isEmpty || salesValues.length < 2) return;
    
    final double maxSales = salesValues.reduce((curr, next) => curr > next ? curr : next);
    final double maxExpenses = expensesValues.isNotEmpty
        ? expensesValues.reduce((curr, next) => curr > next ? curr : next)
        : 0.0;
    final double maxValue = maxSales > maxExpenses ? maxSales : maxExpenses;
    final double scale = maxValue == 0 ? 1 : maxValue;
    
    // Draw Sales Line
    _drawLine(canvas, size, salesValues, scale, const Color(0xFF6366F1), const Color(0xFF8B5CF6));
    
    // Draw Expenses Line
    if (expensesValues.isNotEmpty) {
      _drawLine(canvas, size, expensesValues, scale, const Color(0xFFEF4444), const Color(0xFFF87171));
    }
  }

  void _drawLine(Canvas canvas, Size size, List<double> values, double scale, Color mainColor, Color dotColor) {
    final paint = Paint()
      ..color = mainColor
      ..style = PaintingStyle.stroke
      ..strokeWidth = 3.5
      ..strokeCap = StrokeCap.round;

    final path = Path();
    final double widthBetween = size.width / (values.length - 1);

    final points = <Offset>[];
    for (int i = 0; i < values.length; i++) {
      final x = i * widthBetween;
      final y = size.height - (values[i] / scale * size.height * 0.75) - 8;
      points.add(Offset(x, y));
    }

    path.moveTo(points[0].dx, points[0].dy);
    for (int i = 0; i < points.length - 1; i++) {
      final p0 = points[i];
      final p1 = points[i + 1];
      final controlPoint1 = Offset(p0.dx + widthBetween / 2, p0.dy);
      final controlPoint2 = Offset(p1.dx - widthBetween / 2, p1.dy);
      path.cubicTo(controlPoint1.dx, controlPoint1.dy, controlPoint2.dx, controlPoint2.dy, p1.dx, p1.dy);
    }

    final fillPath = Path.from(path)
      ..lineTo(size.width, size.height)
      ..lineTo(0, size.height)
      ..close();

    final fillPaint = Paint()
      ..shader = LinearGradient(
        colors: [
          mainColor.withOpacity(0.20),
          mainColor.withOpacity(0.0),
        ],
        begin: Alignment.topCenter,
        end: Alignment.bottomCenter,
      ).createShader(Rect.fromLTWH(0, 0, size.width, size.height));

    canvas.drawPath(fillPath, fillPaint);
    canvas.drawPath(path, paint);

    final dotPaint = Paint()
      ..color = dotColor
      ..style = PaintingStyle.fill;
    final outerDotPaint = Paint()
      ..color = isDark ? Colors.white : const Color(0xFF1E2937)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 2;

    for (var pt in points) {
      canvas.drawCircle(pt, 4.5, dotPaint);
      canvas.drawCircle(pt, 4.5, outerDotPaint);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => true;
}
