export const colorsBgLight = {
  white: "bg-white text-black",
  light: "bg-white text-forest-400 border-gray-600",
  contrast: "bg-gray-800 text-white border-gray-900",
  success: "bg-emerald-100 text-emerald-500 border-emerald-600",
  danger: "bg-red-100 text-red-500 border-red-600",
  warning: "bg-yellow-100 text-yellow-500 border-yellow-600",
  info: "bg-blue-100 text-blue-500 border-blue-600",
};

export const getButtonColor = (
  color,
  isOutlined,
  hasHover,
  isActive = false
) => {
  const colors = {
    ring: {
      white: "ring-gray-200",
      whiteDark: "ring-gray-200",
      lightDark: "ring-medic-200",
      contrast: "ring-gray-300",
      success: "ring-success-400",
      danger: "ring-red-300",
      warning: "ring-warning-300",
      info: "ring-medic-100",
    },
    active: {
      white: "bg-gray-100",
      whiteDark: "bg-gray-100",
      lightDark: "bg-medic-200",
      contrast: "bg-gray-700",
      success: "bg-success-400",
      danger: "bg-red-700",
      warning: "bg-warning-400",
      info: "bg-medic-200",
    },
    bg: {
      white: "bg-white text-black",
      whiteDark: "bg-white text-black",
      lightDark: "bg-transparent text-medic-500",
      contrast: "bg-gray-800 text-white",
      success: "bg-success-400 text-white",
      danger: "bg-error-400  text-white",
      warning: "bg-warning-300  text-white",
      info: "bg-medic-500 text-white",
    },
    bgHover: {
      white: "hover:bg-gray-100",
      whiteDark: "hover:bg-gray-100",
      lightDark: "hover:bg-medic-100 hover:text-white ",
      contrast: "hover:bg-gray-700",
      success: "hover:bg-emerald-800 hover:border-emerald-800",
      danger: "hover:bg-red-800 hover:border-red-800",
      warning: "hover:bg-warning-400 hover:border-warning-400",
      info: "hover:bg-medic-400/85 hover:border-medic-400 ",
    },
    borders: {
      white: "border-white",
      whiteDark: "border-white",
      lightDark: "border-medic-300",
      contrast: "border-gray-800",
      success: "border-success-400",
      danger: "border-red-600",
      warning: "border-warning-300",
      info: "border-medic-100",
    },
    text: {
      white: "text-black",
      whiteDark: "text-black",
      lightDark: "text-medic-500",
      contrast: "dark:text-slate-100",
      success: "text-success-300",
      danger: "text-red-600",
      warning: "text-warning-300",
      info: "text-medic-100",
    },
    outlineHover: {
      contrast: "hover:bg-gray-800 hover:text-gray-100",
      success: "hover:bg-success-300 hover:text-white",
      danger: "hover:bg-red-600 hover:text-white",
      warning: "hover:bg-warning-300 hover:text-white",
      info: "hover:bg-medic-100 hover:text-white",
    },
  };
  
  if (!colors.bg[color]) {
    return [color]; // Devolver como array para consistencia
  }

  const isOutlinedProcessed =
    isOutlined && ["white", "whiteDark", "lightDark"].indexOf(color) < 0;

  const base = [colors.borders[color], colors.ring[color]].filter(Boolean);

  if (isActive) {
    if (colors.active[color]) {
      base.push(colors.active[color]);
    }
  } else {
    const colorClass = isOutlinedProcessed ? colors.text[color] : colors.bg[color];
    if (colorClass) {
      base.push(colorClass);
    }
  }

  if (hasHover) {
    const hoverClass = isOutlinedProcessed ? colors.outlineHover[color] : colors.bgHover[color];
    if (hoverClass) {
      base.push(hoverClass);
    }
  }

  return base;
};